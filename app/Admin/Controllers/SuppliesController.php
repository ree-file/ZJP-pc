<?php

namespace App\Admin\Controllers;

use App\Supply;

use App\User;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class SuppliesController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('申请');
            $content->description('列表');

            $content->body($this->grid());

        });
    }
    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('申请');
            $content->description('编辑');

            $content->body($this->form()->edit($id));

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    protected function grid()
    {
        return Admin::grid(Supply::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
			$grid->user('用户邮箱')->display(function ($user) {
				return $user['email'];
			});
			$grid->type('类型')->sortable()->display(function ($type) {
				return $type == 'save' ? '充值' : '提现';
			});
			$grid->money('金额')->sortable();
			$grid->status('状态')->sortable()->display(function ($status) {
				if ($status == 'accepted') return '<span class="text-success">已接受</span>';
				if ($status == 'rejected') return '<span class="text-danger">已拒绝</span>';
				return '<span class="text-warning">处理中</span>';
			});
            $grid->created_at('创建于')->sortable();
            $grid->updated_at('更新于')->sortable();

            // 添加过滤分类
			$grid->filter(function($filter){
				$filter->where(function ($query) {
					$query->whereHas('user', function ($query) {
						$query->where('email', 'like', "%{$this->input}%");
					});
				}, '用户邮箱');
				$filter->equal('type', '类型')->radio([
					''   => '所有',
					'save'    => '充值',
					'get'    => '提现'
				]);
				$filter->equal('status', '状态')->radio([
					''   => '所有',
					'accepted'    => '已接受',
					'rejected'    => '已拒绝',
					'processing'  => '处理中'
				]);
			});

			// 取消默认创建功能、取消默认删除功能、取消默认批量删除功能
			$grid->disableCreation();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
			});
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Supply::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->display('user.email', '用户邮箱');
			$form->image('image', '上传图片内容');
			$form->display('type', '类型')->with(function ($type) {
				return $type == 'save' ? '充值' : '提现';
			});
			$form->display('card_number', '银行卡号');
			$form->display('money', '金额');
			$form->display('status', '状态')->with(function ($status) {
				if ($status == 'accepted') return '<span class="text-success">已接受</span>';
				if ($status == 'rejected') return '<span class="text-danger">已拒绝</span>';
				return '<span class="text-warning">处理中</span>';
			});
			$form->display('created_at', '创建于');
			$form->display('updated_at', '更新于');

			// 处理操作
			$form->divider();
			$form->select('doStatus', '操作状态')->options([
				'accepted' => '接受',
				'rejected' => '拒绝'
			])->help('只能操作处理中的请求，若用户活动资金不足、提取超过上限或订单已操作，此次操作将失效。');
			$form->select('message', '附加信息')->options([
				'申请失败',
				'提现已达日上限',
				'提现已达总上限',
				'活动资金不足，无法发起提现请求'
			])->help('选择处理失败时，可返回给用户失败信息。');

			// 忽略图片上传字段，只用来显示
			$form->ignore(['image']);
        });
    }

	public function update(Request $request, Supply $supply)
	{
		// 若申请非为处理状态，则返回错误
		if ($supply->status != 'processing') {
			$error = new MessageBag([
				'title'   => '操作失败',
				'message' => '该请求已被处理',
			]);
			return back()->withInput()->with(compact('error'));
		}

		// 若未选择处理操作，则返回错误
		if ($request->doStatus == null) {
			$error = new MessageBag([
				'title'   => '操作失败',
				'message' => '该选择操作状态',
			]);
			return back()->withInput()->with(compact('error'));
		}

		// 操作为拒绝
		if ($request->doStatus == 'rejected') {
			$supply->status = 'rejected';
			$supply->message = $request->message;
			$supply->save();
			admin_toastr(trans('admin.update_succeeded'));
			return redirect('/'.config('admin.route.prefix').'/supplies');
		}

		// 操作为接受
		if ($request->doStatus == 'accepted') {
			DB::beginTransaction();
			try {
				$user = User::where('id', $supply->user->id)->lockForUpdate()->first();
				if ($supply->type == 'save') {
					$user->money_active = $user->money_active + $supply->money;
				}
				if ($supply->type == 'get') {
					$historySupplies = Supply::where('user_id', $user->id)->where('type', 'get')->where('status', 'accepted')->get();
					$dailySupplies = Supply::where('user_id', $user->id)
						->where('type', 'get')
						->where('status', 'accepted')
						->where('created_at', '>', Carbon::parse($supply->created_at)->toDateString())
						->where('created_at', '<', Carbon::parse($supply->created_at)->addDay(1)->toDateString())
						->get();

					if ($historySupplies->sum('money') + $supply->money > $user->cash_limit) {
						throw new \Exception('用户可提取金额已达总上限。');
					}

					if ($dailySupplies->sum('money') + $supply->money > config('zjp.USER_DAILY_CASH_LIMIT')) {
						throw new \Exception('用户可提取金额已达日上限。');
					}

					if ($user->money_active < $supply->money) {
						throw new \Exception('用户活动资金不足。');
					}

					$user->money_active = $user->money_active - $supply->money;
				}
				$user->save();
				DB::commit();
			} catch (\Exception $e) {
				DB::rollBack();
				$error = new MessageBag([
					'title'   => '操作失败',
					'message' => $e->getMessage(),
				]);
				return back()->withInput()->with(compact('error'));
			}
			$supply->status = 'accepted';
			$supply->save();
			admin_toastr(trans('admin.update_succeeded'));
			return redirect('/'.config('admin.route.prefix').'/supplies');
		}
	}
}

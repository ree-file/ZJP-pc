<?php

namespace App\Admin\Controllers;

use App\Supply;

use App\User;
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

            $content->header('款项请求');
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

            $content->header('款项请求');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
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
			$grid->disableCreation();
			$grid->disableRowSelector();
			$grid->filter(function($filter){
				// 在这里添加字段过滤器
				$filter->where(function ($query) {
					$query->whereHas('user', function ($query) {
						$query->where('email', 'like', "%{$this->input}%");
					});
				}, '用户邮箱');
			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
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
			$form->display('type', '类型')->with(function ($type) {
				return $type == 'save' ? '充值' : '提现';
			});
			$form->display('status', '状态')->with(function ($status) {
				if ($status == 'accepted') return '<span class="text-success">已接受</span>';
				if ($status == 'rejected') return '<span class="text-danger">已拒绝</span>';
				return '<span class="text-warning">处理中</span>';
			});
			$form->display('card_number', '银行卡号');
			$form->display('money', '金额');
			$form->display('message', '附加信息');
			$form->image('image', '上传图片内容');
            $form->display('created_at', '创建于');
            $form->display('updated_at', '更新于');
            $form->divider();
			$form->select('doStatus', '操作状态')->options([
				'accepted' => '接受',
				'rejected' => '拒绝'
			]);
			$form->number('doMoney', '操作金额')
				->attribute(['min' => 0])
				->help('只能操作处理中的请求，将根据类型和操作金额操作用户相应活动资金。充值类型充入操作金额，提现类型将减去操作金额。若用户活动资金不足或订单已操作，此次操作将失效。');
			$form->ignore(['image']);
        });
    }

	public function update(Request $request, Supply $supply)
	{
		if ($supply->status != 'processing') {
			$error = new MessageBag([
				'title'   => '操作失败',
				'message' => '该请求已被处理',
			]);
			return back()->withInput()->with(compact('error'));
		}

		if ($request->doStatus == null) {
			$error = new MessageBag([
				'title'   => '操作失败',
				'message' => '该选择操作状态',
			]);
			return back()->withInput()->with(compact('error'));
		}

		if ($request->doStatus == 'rejected') {
			$supply->status = 'rejected';
			$supply->save();
			admin_toastr(trans('admin.update_succeeded'));
			return redirect('/'.config('admin.route.prefix').'/supplies');
		}

		if ($request->doStatus == 'accepted') {
			DB::beginTransaction();
			try {
				$user = User::where('id', $supply->user->id)->lockForUpdate()->first();
				if ($supply->type == 'save') {
					$user->money_active = $user->money_active + $request->doMoney;
				}
				if ($supply->type == 'get') {
					if ($user->money_active < $request->doMoney) {
						throw new \Exception('用户活动资金不足。');
					}
					$user->money_active = $user->money_active - $request->doMoney;
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

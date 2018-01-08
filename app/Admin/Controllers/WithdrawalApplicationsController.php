<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\WithdrawalApplication;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Request;

class WithdrawalApplicationsController extends Controller
{
    use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {

			$content->header('提现申请');
			$content->description('列表');

			$content->body($this->grid());
		});
	}

	protected function grid()
	{
		return Admin::grid(WithdrawalApplication::class, function (Grid $grid) {
			$grid->model()->orderBy('id', 'desc');
			$grid->id('ID')->sortable();
			$grid->user_id('用户ID');
			$grid->money('提现金额')->sortable();
			$grid->status('状态')->sortable()->display(function ($status) {
				if ($status == 'accepted') return '<strong class="text-success">已接受</strong>';
				if ($status == 'rejected') return '<strong class="text-danger">已拒绝</strong>';
				return '<strong class="text-warning">处理中</strong>';
			});
			$grid->created_at('创建于');

			// 添加过滤字段
			$grid->filter(function($filter){
				$filter->equal('user_id', '用户ID');
				$filter->equal('status', '状态')->radio([
					''   => '所有',
					'accepted'    => '已接受',
					'rejected'    => '已拒绝',
					'processing'  => '处理中'
				]);
			});

			// 取消批量删除
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});

			// 取消删除
			$grid->actions(function ($actions) {
				$actions->disableDelete();
			});

			// 取消创建
			$grid->disableCreation();
		});
	}

	protected function form()
	{
		return Admin::form(WithdrawalApplication::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->display('user_id', '用户ID');
			$form->display('card_number', '银行卡号');
			$form->display('money', '金额');
			$form->display('status', '状态')->with(function ($status) {
				if ($status == 'accepted') return '<strong class="text-success">已接受</strong>';
				if ($status == 'rejected') return '<strong class="text-danger">已拒绝</strong>';
				return '<strong class="text-warning">处理中</strong>';
			});
			$form->display('created_at', '创建于');
			$form->display('updated_at', '更新于');

			$feeRate = config('website.WITHDRAWAL_FEE_RATE');

			// 处理操作
			$form->divider();
			$form->select('doStatus', '操作状态')->options([
				'accepted' => '接受',
				'rejected' => '拒绝'
			])->help("只允许操作处理中的申请；拒绝请选择附加信息，金额将打回用户账号内。操作人员自行把将提现申请金额扣除手续费比例{$feeRate}打入用户银行卡号。");

			$form->select('message', '返回附加信息')->options([
				'默认拒绝申请' => '默认拒绝申请',
				'申请的银行卡号不正确' => '申请的银行卡号不正确',
				'今日资金流动额过多，操作繁忙，请明日重试' => '今日资金流动额过多，操作繁忙，请明日重试'
			])->help('拒绝申请时，选择返回给用户的附加信息，金额将会打回用户账号内。申请接受时忽略。');

			// 忽略图片上传字段，只用来显示
			$form->ignore(['image']);
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {

			$content->header('提现申请');
			$content->description('编辑');

			$content->body($this->form()->edit($id));
		});
	}

	public function update(Request $request, $id)
	{
		$withdrawalApplication = WithdrawalApplication::findOrFail($id);

		// 若申请非为处理状态，则返回错误
		if ($withdrawalApplication->status != 'processing') {
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
			DB::beginTransaction();
			try {
				// 锁定用户
				$user = User::where('id', $withdrawalApplication->user->id)->lockForUpdate()->first();
				$user->money_withdrawal = $user->money_withdrawal + $withdrawalApplication->money;

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
			$withdrawalApplication->status = 'rejected';
			$withdrawalApplication->message = $request->message;
			$withdrawalApplication->save();
			admin_toastr(trans('admin.update_succeeded'));
			return back();
		}

		// 操作为接受
		if ($request->doStatus == 'accepted') {
			$withdrawalApplication->status = 'accepted';
			$withdrawalApplication->save();
			admin_toastr(trans('admin.update_succeeded'));
			return back();
		}
	}
}

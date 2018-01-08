<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\RechargeApplication;
use App\User;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Request;

class RechargeApplicationsController extends Controller
{
    use ModelForm;

	public function index()
	{
		return Admin::content(function (Content $content) {

			$content->header('充值申请');
			$content->description('列表');

			$content->body($this->grid());
		});
	}

	protected function grid()
	{
		return Admin::grid(RechargeApplication::class, function (Grid $grid) {
			$grid->model()->orderBy('id', 'desc');
			$grid->id('ID')->sortable();
			$grid->user_id('用户ID');
			$grid->money('充值金额')->sortable();
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
		return Admin::form(RechargeApplication::class, function (Form $form) {
			$form->display('id', 'ID');
			$form->display('user_id', '用户ID');
			$form->image('image', '上传图片内容');
			$form->display('card_number', '银行卡号');
			$form->display('money', '金额');
			$form->display('status', '状态')->with(function ($status) {
				if ($status == 'accepted') return '<strong class="text-success">已接受</strong>';
				if ($status == 'rejected') return '<strong class="text-danger">已拒绝</strong>';
				return '<strong class="text-warning">处理中</strong>';
			});
			$form->display('created_at', '创建于');
			$form->display('updated_at', '更新于');

			// 处理操作
			$form->divider();
			$form->select('doStatus', '操作状态')->options([
				'accepted' => '接受',
				'rejected' => '拒绝'
			])->help('只允许操作处理中的申请；接受通过后资金将按金额打入用户账户内；拒绝则选择附加信息。');

			$form->select('message', '返回附加信息')->options([
				'默认拒绝申请' => '默认拒绝申请',
				'申请的充值金额不正确' => '申请的充值金额不正确',
				'申请的银行卡号不正确' => '申请的银行卡号不正确',
				'提供的信息不准确' => '提供的信息不准确'
			])->help('拒绝申请时，选择返回给用户的附加信息。申请接受时忽略。');

			// 忽略图片上传字段，只用来显示
			$form->ignore(['image']);
		});
	}

	public function edit($id)
	{
		return Admin::content(function (Content $content) use ($id) {

			$content->header('充值申请');
			$content->description('查看与编辑');

			$content->body($this->form()->edit($id));
		});
	}

	public function update(Request $request, $id)
	{
		$rechargeApplication = RechargeApplication::findOrFail($id);

		// 若申请非为处理状态，则返回错误
		if ($rechargeApplication->status != 'processing') {
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
			$rechargeApplication->status = 'rejected';
			$rechargeApplication->message = $request->message;
			$rechargeApplication->save();
			admin_toastr(trans('admin.update_succeeded'));
			return back();
		}

		// 操作为接受
		if ($request->doStatus == 'accepted') {
			DB::beginTransaction();
			try {
				// 锁定用户
				$user = User::where('id', $rechargeApplication->user->id)->lockForUpdate()->first();
				$user->money_withdrawal = $user->money_withdrawal + $rechargeApplication->money;

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
			$rechargeApplication->status = 'accepted';
			$rechargeApplication->save();
			admin_toastr(trans('admin.update_succeeded'));
			return back();
		}
	}
}

<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Order;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Request;

class OrdersController extends Controller
{
	public function index(Request $request)
	{
		return Admin::content(function (Content $content) {
			$content->header('市场');
			$content->description('列表');
			$content->body($this->grid());
		});
	}

	protected function grid()
	{
		return Admin::grid(Order::class, function (Grid $grid) {
			$grid->id('ID')->sortable();
			$grid->nest('巢名')->display(function ($nest) {
				return $nest['name'];
			});
			$grid->status('状态')->sortable()->display(function ($status) {
				if ($status == 'finished') return '<span class="text-success">已交易</span>';
				if ($status == 'abandoned') return '<span class="text-danger">已下架</span>';
				return '<span class="text-warning">售卖中</span>';
			});
			$grid->price('价格')->sortable();
			$grid->seller('售卖者')->display(function ($seller) {
				return $seller['email'];
			});
			$grid->buyer('购买者')->display(function ($buyer) {
				if ($buyer) {
					return $buyer['email'];
				} else {
					return '';
				}
			});
			$grid->created_at('创建于');

			// 添加过滤分类
			$grid->filter(function($filter){
				$filter->where(function ($query) {
					$query->whereHas('nest', function ($query) {
						$query->where('name', 'like', "%{$this->input}%");
					});
				}, '巢名');
				$filter->where(function ($query) {
					$query->whereHas('seller', function ($query) {
						$query->where('email', 'like', "%{$this->input}%");
					});
				}, '售卖者');
				$filter->equal('status', '状态')->radio([
					''   => '所有',
					'finished'    => '已交易',
					'abandoned'    => '已下架',
					'selling'  => '售卖中'
				]);
			});

			// 取消默认创建功能、取消默认删除功能、取消默认批量删除功能、编辑功能
			$grid->disableCreation();
			$grid->actions(function ($actions) {
				$actions->append('<a href="/'.config('admin.route.prefix').'/orders/'.$actions->getKey().'/abandon"><i class="fa fa-arrow-down"></i></a>');
				$actions->disableDelete();
				$actions->disableEdit();
			});
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
		});
	}

	public function abandon(Order $order)
	{
		if ($order->status != 'selling') {
			$error = new MessageBag([
				'title'   => '操作失败',
				'message' => '该市场单为非上架状态',
			]);
			return back()->withInput()->with(compact('error'));
		}
		$order->status = 'abandoned';
		$order->save();

		admin_toastr(trans('admin.update_succeeded'));
		return back();
	}
}

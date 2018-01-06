<?php

namespace App\Admin\Controllers;

use App\TransactionRecord;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TransactionRecordsController extends Controller
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

            $content->header('交易记录');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(TransactionRecord::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

			$grid->id('ID')->sortable();
			$grid->column('seller.email', '卖家邮箱');
			$grid->column('buyer.email', '买家邮箱');
			$grid->column('nest.name', '猫窝名');
			$grid->price('售出价格')->sortable();
			$grid->income('卖家收款');
			$grid->created_at('创建于');

			$grid->filter(function($filter){
				$filter->equal('seller_id', '卖家ID');
				$filter->equal('buyer_id', '买家ID');
				$filter->equal('nest_id', '猫窝ID');
			});

			// 取消创建
			$grid->disableCreation();
			// 禁用操作列
			$grid->disableActions();
			// 取消批量删除
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
        });
    }

}

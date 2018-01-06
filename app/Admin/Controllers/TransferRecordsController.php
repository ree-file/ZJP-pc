<?php

namespace App\Admin\Controllers;

use App\TransferRecord;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TransferRecordsController extends Controller
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

            $content->header('转账记录');
            $content->description('列表');

            $content->body($this->grid());
        });
    }


    protected function grid()
    {
        return Admin::grid(TransferRecord::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

            $grid->id('ID')->sortable();
            $grid->column('payer.email', '转款者邮箱');
            $grid->column('receiver.email', '收款者邮箱');
            $grid->money('金额')->sortable();

            $grid->created_at('创建于');

			$grid->filter(function($filter){
				$filter->equal('payer_id', '转款者ID');
				$filter->equal('receiver_id', '收款者ID');
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

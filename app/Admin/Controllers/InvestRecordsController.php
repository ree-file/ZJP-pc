<?php

namespace App\Admin\Controllers;

use App\InvestRecord;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class InvestRecordsController extends Controller
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

            $content->header('投资记录');
            $content->description('列表');

            $content->body($this->grid());
        });
    }


    protected function grid()
    {
        return Admin::grid(InvestRecord::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

			$grid->id('ID')->sortable();
			$grid->column('user.email', '用户邮箱');
			$grid->column('nest.name', '猫窝名');
			$grid->type('类型')->display(function ($type) {
				if ($type == 'reinvest') return "<strong class='text-blue'>复投</strong>";
				if ($type == 'upgrade') return "<strong class='text-red'>升单</strong>";
				return "<strong class='text-green'>创建</strong>";
			});
			$grid->eggs('蛋数')->sortable();
			$grid->created_at('创建于');

			$grid->filter(function($filter){
				$filter->equal('user_id', '用户ID');
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

<?php

namespace App\Admin\Controllers;

use App\Contract;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ContractsController extends Controller
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

            $content->header('合约');
            $content->description('列表');

            $content->body($this->grid());
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Contract::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

			$grid->id('ID')->sortable();
			$grid->column('nest.name', '猫窝名');
			$grid->is_finished('是否完成')->display(function ($boolean) {
				if ($boolean) return "<strong class='text-green'>已完成</strong>";
				return "<strong class='text-red'>未完成</strong>";
			});
			$grid->eggs('蛋数')->sortable();
			$grid->hatches('孵化数')->sortable();
			$grid->created_at('创建于');

			$grid->filter(function($filter){
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

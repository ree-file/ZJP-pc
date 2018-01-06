<?php

namespace App\Admin\Controllers;

use App\Card;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CardsController extends Controller
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

            $content->header('银行卡');
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
        return Admin::grid(Card::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

            $grid->id('ID')->sortable();
            $grid->bankname('银行名');
            $grid->username('账户名');
            $grid->number('卡号');
            $grid->column('user.email', '用户邮箱');
            $grid->created_at('创建于');

			$grid->filter(function($filter){
				$filter->like('bankname', '银行名');
				$filter->like('username', '账户名');
				$filter->like('number', '卡号');
				$filter->equal('user_id', '用户ID');
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

<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab;

class UsersController extends Controller
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

            $content->header('用户');
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

            $content->header('用户');
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
        return Admin::grid(User::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
			$grid->email('邮箱');
			$grid->is_freezed('是否冻结')->switch();
			$grid->money_active('交易资金')->sortable();
			$grid->money_limit('激活资金')->sortable();
			$grid->money_market('市场资金')->sortable();

            $grid->created_at('创建于')->sortable();

			$grid->disableCreation();
			$grid->disableRowSelector();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
			});
			$grid->filter(function($filter){
				// 在这里添加字段过滤器
				$filter->like('email', '邮箱');
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
		return Admin::form(User::class, function (Form $form) {

			$form->tab('基本信息', function (Form $form) {
				$form->display('id', 'ID');
				$form->display('email', '邮箱');

				$form->number('money_active', '交易资金')->attribute('min', 0);
				$form->number('money_limit', '激活资金')->attribute('min', 0);
				$form->number('money_market', '市场资金')->attribute('min', 0);
				$form->switch('is_freezed', '是否冻结');
				$form->display('created_at', '创建于');
				$form->display('updated_at', '更新于');
			})->tab('银行卡', function (Form $form) {
				$form->hasMany('cards', '', function (Form\NestedForm $form) {
					$form->text('number', '卡号');
					$form->text('username', '账户名');
					$form->text('bankname', '银行名');
				});
			});
		});
	}
}

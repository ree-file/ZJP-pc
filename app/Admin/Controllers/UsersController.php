<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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

            $content->header('会员');
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

            $content->header('会员');
            $content->description('编辑');

            $content->body($this->form_edit()->edit($id));
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

            $content->header('会员');
            $content->description('创建');

            $content->body($this->form_create());
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
			$grid->is_freezed('冻结状态')->display(function ($is_freezed) {
				return $is_freezed ? '<span class="text-danger">是</span>' : '<span class="text-info">否</span>';
			});
			$grid->money_active('交易资金');
			$grid->money_limit('激活资金');
			$grid->money_market('市场资金');
			$grid->nests('合约数')->display(function ($nests) {
				$count = count($nests);
				return $count;
			});
            $grid->created_at('创建于');
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

            $form->display('id', 'ID');

            $form->text('email', '邮箱');

			$form->number('money_active', '交易资金')->attribute('min', 0);
			$form->number('money_limit', '激活资金')->attribute('min', 0);
			$form->number('money_market', '市场资金')->attribute('min', 0);

			$form->switch('is_freezed', '是否冻结');

            $form->display('created_at', '创建于');
            $form->display('updated_at', '更新于');
        });
    }

    public function form_create()
	{
		return Admin::form(User::class, function (Form $form) {
			$form->text('email', '邮箱');
			$form->password('password', '密码')->rules('required|min:6');
			$form->switch('is_freezed', '是否冻结')->default(true);
		});
	}

    public function store()
	{
		return $this->form_create()->store();
	}

	public function form_edit()
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
					$form->switch('is_passed', '是否过审')->default(false);
				});
			});
		});
	}

	public function update($id)
	{
		return $this->form_edit()->update($id);
	}
}

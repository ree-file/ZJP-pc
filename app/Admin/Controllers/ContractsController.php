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
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
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

            $content->header('合约');
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
        return Admin::grid(Contract::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->eggs('币数');
            $grid->is_finished('是否完成')->display(function ($is_finished) {
            	return $is_finished ? '<span class="text-success">已完成</span>' : '<span class="text-danger">未完成</span>';
			});
			$grid->column('nest.name', '天使猫');
            $grid->created_at('创建于')->sortable();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Contract::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function form_create()
	{
		return Admin::form(Contract::class, function (Form $form) {
			$form->radio('eggs', '币数')->options([
				50 => '50',
				150 => '150',
				500 => '500',
				1500 => '1500'
 			]);
		});
	}
}

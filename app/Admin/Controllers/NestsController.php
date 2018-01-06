<?php

namespace App\Admin\Controllers;

use App\Nest;

use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class NestsController extends Controller
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

            $content->header('猫窝');
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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Nest::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

			// 猫窝信息
			$grid->id('ID')->sortable();
			$grid->name('名字');
			$grid->contracts('合约数')->display(function ($contracts) {
				$count = count($contracts);
				return "<span>{$count}</span>";
			});
			$grid->is_selling('是否在售')->display(function ($boolean) {
				return $boolean ? "<strong class='text-success'>是</strong>" : "<strong>否</strong>";
			})->sortable();
			$grid->column('user.email', '窝主邮箱');
            $grid->created_at('创建于')->sortable();

			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->append('<a href="/'.config('admin.route.prefix').'/nests/'.$actions->getKey().'"><i class="fa fa-eye"></i></a>');
			});

			$grid->filter(function($filter){
				// 在这里添加字段过滤器
				$filter->like('name', '名字');
				$filter->equal('user_id', '用户ID');
			});

			// 取消创建
			$grid->disableCreation();
			// 取消批量删除
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
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
        return Admin::form(Nest::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

	public function show($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->header('猫窝');
			$content->description('查看');

			$nest = Nest::where('id', $id)->with('contracts')->first();
			$content->body(view('admin.models.nests.show', compact('nest', 'contracts', 'grandchildren')));
		});
	}
}

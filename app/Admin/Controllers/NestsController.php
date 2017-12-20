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

            $content->header('巢');
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

            $content->header('天使猫');
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
        return Admin::grid(Nest::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
			$grid->name('名字');
			$grid->community('社区')->sortable();
			$grid->column('user.email', '用户邮箱');

            $grid->created_at('创建于')->sortable();

			$grid->disableCreation();
			$grid->disableRowSelector();
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->append('<a href="/'.config('admin.route.prefix').'/nests/'.$actions->getKey().'"><i class="fa fa-eye"></i></a>');
			});
			$grid->filter(function($filter){
				// 在这里添加字段过滤器
				$filter->like('name', '名字');
				$filter->like('community', '社区');
				$filter->where(function ($query) {
					$query->whereHas('user', function ($query) {
						$query->where('email', 'like', "%{$this->input}%");
					});
				}, '用户邮箱');
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

    protected function form_create()
	{
		return Admin::form(Nest::class, function (Form $form) {

			$form->text('name', '名字')->rules('required|unique:nests');
			$form->select('user_id', '用户')->options(function ($id) {
				$user = User::find($id);

				if ($user) {
					return [$user->id => $user->email];
				}
			})->ajax('/admin/api/users')->rules('required');
			$form->select('inviter_id', '邀请天使猫')->options(function ($id) {
				$nest = Nest::find($id);

				if ($nest) {
					return [$nest->id => $nest->name];
				}
			})->ajax('/admin/api/nests')->rules('required');
			$form->select('parent_id', '上级天使猫')->options(function ($id) {
				$nest = Nest::find($id);

				if ($nest) {
					return [$nest->id => $nest->name];
				}
			})->ajax('/admin/api/nests')->rules('required');
			$form->radio('community', '社区')->options([
				'A' => 'A',
				'B' => 'B',
				'C' => 'C'])->default('A');
			$form->radio('contract.eggs', '初始合约币数')->options([
				50 => 50,
				150 => 150,
				500 => 500,
				1500 => 1500
			])->default(50)->rules('numeric');

		});
	}

	public function form_edit()
	{
		return Admin::form(Nest::class, function (Form $form) {

			$form->display('name', '名字');
			$form->select('user_id', '用户')->options(function ($id) {
				$user = User::find($id);

				if ($user) {
					return [$user->id => $user->email];
				}
			})->ajax('/admin/api/users');
			$form->select('inviter_id', '邀请天使猫')->options(function ($id) {
				$nest = Nest::find($id);

				if ($nest) {
					return [$nest->id => $nest->name];
				}
			})->ajax('/admin/api/nests');
			$form->select('parent_id', '上级天使猫')->options(function ($id) {
				$nest = Nest::find($id);

				if ($nest) {
					return [$nest->id => $nest->name];
				}
			})->ajax('/admin/api/nests');

			$form->radio('community', '社区')->options([
				'A' => 'A',
				'B' => 'B',
				'C' => 'C'])->default('A');
		});
	}

	public function show($id)
	{
		return Admin::content(function (Content $content) use ($id) {

			$content->header('巢');
			$content->description('查看');

			$nest = Nest::where('id', $id)->with('children', 'receivers', 'children.children', 'contracts')->first();
			$contracts = $nest->contracts->sortByDesc('id');
			$grandchildren = $nest->children->pluck('children')->flatten();

			$content->body(view('admin.nests.show', compact('nest', 'contracts', 'grandchildren')));
		});
	}

	public function nests()
	{
		$q = request()->get('q');
		return Nest::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
	}
}

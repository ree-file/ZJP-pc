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

            $content->header('天使猫');
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
			$grid->community('社区');
			$grid->column('user.email', '用户');

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

	public function store()
	{
		return $this->form_create()->store();
	}

	public function form_edit()
	{
		return Admin::form(Nest::class, function (Form $form) {

			$form->text('name', '名字');
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

			$content->header('天使猫');
			$content->description('详情');

			$nest = Nest::where('id', $id)->with('children', 'receivers', 'children.children', 'contracts')->first();
			$contracts = $nest->contracts->sortByDesc('id')->map(function ($item, $key) {
				return $item->only(['id', 'eggs', 'is_finished', 'created_at']);
			});
			$grandchildren = $nest->children->pluck('children')->flatten();
			$tab = new Tab();
			$table = new Table([], [
				['总合约数', count($nest->contracts)],
				['总币数', $nest->contracts->sum('eggs')],
				['邀请人数', count($nest->receivers)],
				['下级人数', count($nest->children)],
				['下下级人数', count($grandchildren)]
			]);
			$tab->add('关联信息', $table);
			$table = new Table([
				'id', '币数', '是否完成', '创建于'],
				$contracts->toArray()
			);
			$tab->add('合约信息', $table);
			$content->body(new Box('详情', $tab));
		});
	}

	public function nests()
	{
		$q = request()->get('q');
		return Nest::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
	}
}

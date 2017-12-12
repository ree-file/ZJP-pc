<?php

namespace App\Admin\Controllers;

use App\Card;

use App\User;
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
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('银行卡');
            $content->description('编辑');

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

            $content->header('银行卡');
            $content->description('创建');

            $content->body($this->form());
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

            $grid->id('ID')->sortable();
            $grid->number('卡号');
			$grid->username('账户名');
			$grid->bankname('银行名');
			$grid->is_passed('是否过审')->display(function ($is_passed) {
				return $is_passed ? '<span class="text-success">是</span>' : '<span class="text-danger">否</span>';
			});

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Card::class, function (Form $form) {

            $form->display('id', 'ID');

			$form->select('user_id', '用户')->options(function ($id) {
				$user = User::find($id);

				if ($user) {
					return [$user->id => $user->email];
				}
			})->ajax('/admin/api/users');

            $form->text('number', '卡号');
            $form->text('username', '账户名');
			$form->text('bankname', '银行名');
			$form->switch('is_passed', '是否过审')->default(false);


            $form->display('created_at', '创建于');
            $form->display('updated_at', '更新于');
        });
    }

	public function users()
	{
		$q = request()->get('q');
		return User::where('email', 'like', "%$q%")->paginate(null, ['id', 'email as text']);
	}
}

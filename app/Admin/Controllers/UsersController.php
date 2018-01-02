<?php

namespace App\Admin\Controllers;

use App\Contract;
use App\Nest;
use App\Supply;
use App\User;

use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

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
			$grid->cash_limit('提现限制')->sortable();
			$grid->created_at('创建于')->sortable();
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
			});
			$grid->filter(function($filter){
				$filter->like('email', '邮箱');
			});
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
            $nests = Nest::where('user_id', $id)->with('receivers')->get();
            $receviers = $nests->pluck('receivers')->flatten();
            $supplies = Supply::where('user_id', $id)->get();
            $money = $supplies->where('type', 'get')->where('status', 'accepted')->sum('money');
            $analyse = [
            	count($nests),
				count($receviers),
				$money
			];
            $tab = new Tab();
            $tab->add('基本信息', $this->form($id)->edit($id));
            $tab->add('统计信息', view('admin.models.users._analyse', compact('analyse')));
			$tab->add('银行卡', $this->form2()->edit($id));
            $tab->add('巢', view('admin.models.users._nests', compact('nests')));
			$tab->add('项款请求', view('admin.models.users._supplies', compact('supplies')));
            $content->body($tab);
        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
	protected function form($id = null)
	{
		return Admin::form(User::class, function (Form $form) use ($id) {
			if ($id) {
				$form->display('id', 'ID');
				$form->display('email', '邮箱');
				$form->display('money_active', '交易资金');
				$form->display('money_limit', '激活资金');
				$form->display('money_market', '市场资金');
				$form->switch('is_freezed', '是否冻结');
				$form->number('cash_limit', '提现限制');
				$form->display('created_at', '创建于');
				$form->display('updated_at', '更新于');
			} else {
				$form->email('email', '邮箱')->rules('required|unique:users');
				$form->password('password', '密码')->rules('required');
			}
		});
	}

	protected function form2()
	{
		return Admin::form(User::class, function (Form $form) {
			$form->hasMany('cards', '', function (Form\NestedForm $form) {
				$form->text('number', '卡号');
				$form->text('username', '账户名');
				$form->text('bankname', '银行名');
			});
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
			$content->header('用户');
			$content->description('添加');
			$content->body($this->form());
		});
	}

	public function show($id)
	{
		return $this->edit($id);
	}

	public function store(Request $request)
	{
		DB::transaction(function () use ($request) {
			$user = new User();
			$user->email = $request->email;
			$user->password = bcrypt($request->password);
			$user->save();
			$nest = new Nest();
			$nest->name = rand_name();
			$nest->user_id = $user->id;
			$nest->community = 'A';
			$nest->save();
			$contract = new Contract();
			$contract->nest_id = $nest->id;
			$contract->eggs = config('zjp.CONTRACT_LEVEL_ONE');
			$contract->cycle_date = Carbon::today();
			$contract->save();
		});

		admin_toastr(trans('save_succeeded'));
	}
}

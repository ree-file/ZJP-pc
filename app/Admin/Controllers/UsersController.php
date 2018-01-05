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
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
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
			$grid->coins('猫币')->sortable();
			$grid->withdrawal_limit('提现限制')->sortable();
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
/*            $nests = Nest::where('user_id', $id)->with('receivers')->get();
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
			$tab->add('项款请求', view('admin.models.users._supplies', compact('supplies')));*/
/*			// 找到用户
			$user = User::with('cards', 'nests')->find($id);
			// 用户银行卡
			$cards = $user->cards;
			// 用户猫窝
			$nests = $user->nests;

			$tab = new Tab();

			$form = new \Encore\Admin\Widgets\Form();
			$form->method();
			$form->hidden('_method')->default('PUT');
			$form->display('id', 'ID')->default($user->id);
			$form->display('email', '邮箱')->default($user->email);
			$form->display('money_active', '交易资金')->default($user->money_active);
			$form->display('money_limit', '激活资金')->default($user->money_limit);
			$form->currency('withdrawal_limit', '提现限制')->default($user->withdrawal_limit);
			$form->switch('is_freezed', '是否冻结')->default($user->is_freezed);
			$form->display('created_at', '创建于')->default($user->created_at);
			$form->display('updated_at', '更新于')->default($user->updated_at);

			$tab->add('基本信息', $form);

			// 创建银行卡页面

			$headers = ['ID', '银行', '账户名', '卡号', '创建于'];

			$rows = [];

			foreach ($cards as $card) {
				$row = [$card->id, $card->bankname, $card->username, $card->number, $card->created_at];
				array_push($rows, $row);
			}

			$table = new Table($headers, $rows);

			$box = new Box('银行卡列表', $table);

			$tab->add('银行卡', $box->solid()->style('success'));
			$url = "/".config('admin.route.prefix')."/";
			$tab->add('<span onclick="javascript:window.location.href='.$url.'">猫窝</span>', "");
			$tab->add('<span onclick="javascript:window.location.href='.$url.'">充值记录</span>', "");
			$tab->add('<span onclick="javascript:window.location.href='.$url.'">提现记录</span>', "");
			$tab->add('<span onclick="javascript:window.location.href='.$url.'">市场单</span>', "");*/

			// 创建猫窝页面


			$content->header('用户');
			$content->description('编辑');

			$content->row(function ($row) {
				$row->column(3, new InfoBox('猫窝', 'users', 'aqua', '/demo/users', '1024'));
				$row->column(3, new InfoBox('', 'shopping-cart', 'green', '/demo/orders', '150%'));
				$row->column(3, new InfoBox('Articles', 'book', 'yellow', '/demo/articles', '2786'));
				$row->column(3, new InfoBox('市场单	', 'file', 'red', '/demo/files', '698726'));
			});

            $content->body($this->form($id)->edit($id));
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

				$user = User::with('cards', 'nests')->find($id);
				// 用户银行卡
				$cards = $user->cards;

				$headers = ['ID', '银行', '账户名', '卡号', '创建于'];

				$rows = [];

				foreach ($cards as $card) {
					$row = [$card->id, $card->bankname, $card->username, $card->number, $card->created_at];
					array_push($rows, $row);
				}

				$table = new Table($headers, $rows);

				$box = new Box('银行卡列表', $table);

				$form->tab('基本信息', function ($form) {
					$form->display('id', 'ID');
					$form->display('email', '邮箱');
					$form->display('money_active', '交易资金');
					$form->display('money_limit', '激活资金');
					$form->currency('withdrawal_limit', '提现限制');
					$form->switch('is_freezed', '是否冻结');
					$form->display('created_at', '创建于');
					$form->display('updated_at', '更新于');
				})->tab('银行卡', function ($form) use ($box) {
					$form->html($box);
				});

			} else {
				$form->email('email', '邮箱')->rules('required|unique:users');
				$form->password('password', '密码')->rules('required');
			}
		});
	}

	public function editCards($id)
	{
		return Admin::content(function (Content $content) use($id) {
			$content->header('用户-银行卡');
			$content->description('编辑');

			$content->body($this->formCards()->edit($id));
		});
	}

	protected function formCards()
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

	// 创建用户
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
			$nest->save();

			$contract = new Contract();
			$contract->nest_id = $nest->id;
			$contract->eggs = config('zjp.CONTRACT_LEVEL_ONE');
			$contract->save();
		});

		admin_toastr(trans('admin.save_succeeded'));
	}
}

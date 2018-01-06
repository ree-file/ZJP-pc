<?php

namespace App\Admin\Controllers;

use App\Card;
use App\Contract;
use App\InvestRecord;
use App\Nest;
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

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

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
			$content->header('用户');
			$content->description('查看与编辑');

			$content->row(function ($row) use($id) {
				$user = User::with('nests', 'rechargeApplications', 'withdrawalApplications', 'orders')
					->find($id);

				$url = '/'.config('admin.route.prefix').'/nests?user_id='.$user->id;
				$count = $user->nests->count();
				$row->column(3, new InfoBox('猫窝', 'shopping-bag', 'red', $url, $count));

				$url = '/'.config('admin.route.prefix').'/rechargeApplications?user_id='.$user->id;
				$count = $user->rechargeApplications->count();
				$row->column(3, new InfoBox('充值申请', 'dollar', 'green', $url, $count));

				$url = '/'.config('admin.route.prefix').'/withdrawalApplications?user_id='.$user->id;
				$count = $user->withdrawalApplications->count();
				$row->column(3, new InfoBox('提现申请', 'money', 'yellow', $url, $count));

				$url = '/'.config('admin.route.prefix').'/orders?seller_id='.$user->id;
				$count = $user->orders->count();
				$row->column(3, new InfoBox('市场单	', 'list-alt', 'purple', $url, $count));
			});

            $content->body($this->form($id)->edit($id));
        });
    }

    public function update($id)
	{
		return $this->form($id, true)->update($id);
	}
    /**
     * Make a form builder.
     *
     * @return Form
     */
	protected function form($id = null, $update = false)
	{
		return Admin::form(User::class, function (Form $form) use ($id, $update) {
			if ($id) {
				if ($update) {

					// 如果为上传
					$form->currency('withdrawal_limit', '提现限制');
					$form->switch('is_freezed', '是否冻结');
				} else {

					// 如果为编辑显示
					$form->tab('基本信息', function ($form) {
						$form->display('id', 'ID');
						$form->display('email', '邮箱');
						$form->display('money_active', '交易资金');
						$form->display('money_limit', '激活资金');
						$form->currency('withdrawal_limit', '提现限制');
						$form->switch('is_freezed', '是否冻结');
						$form->display('created_at', '创建于');
						$form->display('updated_at', '更新于');
					})->tab('银行卡', function ($form) use ($id) {
						$cards = Card::where('user_id', $id)->get();

						// 表格头
						$headers = ['ID', '银行', '账户名', '卡号', '创建于'];
						// 表格行
						$rows = [];
						foreach ($cards as $card) {
							$row = [$card->id, $card->bankname, $card->username, $card->number, $card->created_at];
							array_push($rows, $row);
						}

						// 列出银行卡
						$table = new Table($headers, $rows);
						$box = new Box('银行卡列表', $table);

						$form->html($box->solid()->style('success'));
					});
				}
			} else {

				// 如果为创建
				$form->email('email', '邮箱')->rules('required|unique:users');
				$form->password('password', '密码')->rules('required');
			}
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
			$user->withdrawal_limit = config('website.USER_WITHDRAWAL_LIMIT');
			$user->save();

			$nest = new Nest();
			$nest->name = rand_name();
			$nest->user_id = $user->id;
			$nest->save();

			$contract = new Contract();
			$contract->nest_id = $nest->id;
			$contract->eggs = config('website.CONTRACT_LEVEL_ONE');
			$contract->save();

			$investRecord = new \App\InvestRecord();
			$investRecord->nest_id = $nest->id;
			$investRecord->user_id = $user->id;
			$investRecord->contract_id = $contract->id;
			$investRecord->type = 'store';
			$investRecord->eggs = $contract->eggs;
			$investRecord->save();
		});

		admin_toastr(trans('admin.save_succeeded'));
	}
}

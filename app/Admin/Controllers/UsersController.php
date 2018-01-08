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
			$grid->money_withdrawal('可提现资金')->sortable();
			$grid->money_active('活动资金')->sortable();
			$grid->money_limit('限制资金')->sortable();
			$grid->coins('猫币')->sortable();
			$grid->created_at('创建于')->sortable();

			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->append('<a href="/'.config('admin.route.prefix').'/users/'.$actions->getKey().'"><i class="fa fa-eye"></i></a>');
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

            $content->body($this->form($id)->edit($id));
        });
    }

    public function update($id)
	{
		return $this->form($id)->update($id);
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
				$form->display('money_withdrawal', '可提现资金');
				$form->display('money_active', '活动资金');
				$form->display('money_limit', '限制资金');
				$form->display('coins', '猫币');
				$form->switch('is_freezed', '是否冻结');
				$form->display('created_at', '创建于');
				$form->display('updated_at', '更新于');
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
		return Admin::content(function (Content $content) use ($id) {
			$content->header('用户');
			$content->description('查看');

			$user = User::with('nests', 'rechargeApplications', 'withdrawalApplications', 'cards', 'incomeRecords', 'investRecords', 'transferRecordsOfPaying', 'transferRecordsOfReceiving', 'transactionRecordsOfSelling', 'transactionRecordsOfBuying')
				->find($id);

			$content->row(function ($row) use($user) {

				$url = '/'.config('admin.route.prefix').'/nests?user_id='.$user->id;
				$count = $user->nests->count();
				$row->column(3, new InfoBox('猫窝', 'shopping-bag', 'red', $url, $count));

				$url = '/'.config('admin.route.prefix').'/recharge_applications?user_id='.$user->id;
				$count = $user->rechargeApplications->count();
				$row->column(3, new InfoBox('充值申请', 'dollar', 'green', $url, $count));

				$url = '/'.config('admin.route.prefix').'/withdrawal_applications?user_id='.$user->id;
				$count = $user->withdrawalApplications->count();
				$row->column(3, new InfoBox('提现申请', 'money', 'yellow', $url, $count));

				$url = '/'.config('admin.route.prefix').'/cards?user_id='.$user->id;
				$count = $user->cards->count();
				$row->column(3, new InfoBox('银行卡', 'credit-card', 'teal', $url, $count));

				$url = '/'.config('admin.route.prefix').'/invest_records?user_id='.$user->id;
				$count = $user->investRecords->count();
				$row->column(3, new InfoBox('投资记录', 'cube', 'gray', $url, $count));

				$url = '/'.config('admin.route.prefix').'/income_records?user_id='.$user->id;
				$count = $user->incomeRecords->count();
				$row->column(3, new InfoBox('收益记录', 'cube', 'gray', $url, $count));

				$url = '/'.config('admin.route.prefix').'/transfer_records?user_id='.$user->id;
				$count = $user->transferRecordsOfPaying->count();
				$row->column(3, new InfoBox('转账记录（转款）', 'cube', 'gray', $url, $count));

				$url = '/'.config('admin.route.prefix').'/transaction_records?user_id='.$user->id;
				$count = $user->transactionRecordsOfSelling->count();
				$row->column(3, new InfoBox('交易记录（售出）', 'cube', 'gray', $url, $count));
			});

			// 基本信息
			$tab = new Tab();
			$rows = [
				["<strong>ID</strong>", $user->id],
				["<strong>名字</strong>", $user->email],
				["<strong>是否冻结</strong>", $user->is_freezed ? '<strong class="text-info">是</strong>' : '<strong>否</strong>'],
				["<strong>是否有安全密码</strong>", $user->security_code ? '<strong class="text-info">是</strong>' : '<strong class="text-danger">否</strong>'],
				["<strong>可提现资金</strong>", $user->money_withdrawal],
				["<strong>活动资金</strong>", $user->money_active],
				["<strong>限制资金</strong>", $user->money_limit],
				["<strong>猫币</strong>", $user->coins],
				["<strong>创建于</strong>", $user->created_at],
				["<strong>更新于</strong>", $user->updated_at]
			];

			$table = new Table(null, $rows);
			$box = new Box('用户', $table);
			$tab->add('基本信息', $box->style('primary')->solid());

			// 统计信息
			$rechargeMoney = $user->rechargeApplications->where('status', 'accepted')->sum('money');
			$withdrawalMoney = $user->withdrawalApplications->where('status', 'accepted')->sum('money');

			$transferPayed = $user->transferRecordsOfPaying->sum('money');
			$transferReceived = $user->transferRecordsOfReceiving->sum('money');

			$incomeMoneyActive = $user->incomeRecords->sum('money_active');
			$incomeMoneyLimit = $user->incomeRecords->sum('money_limit');
			$incomeCoins = $user->incomeRecords->sum('coins');

			$investEggs = $user->investRecords->sum('eggs');
			$investVal = $user->investRecords->sum('eggs') * config('website.EGG_VAL');

			$transactionPayed = $user->transactionRecordsOfSelling->sum('price');
			$transactionIncome = $user->transactionRecordsOfBuying->sum('income');

			$rows = [
				["<strong class='text-green'>已充值金额</strong>", $rechargeMoney],
				["<strong class='text-orange'>已提现金额（未扣税）</strong>", $withdrawalMoney],
				["<strong class='text-navy'>转账付款</strong>", $transferPayed],
				["<strong class='text-navy'>转账收款</strong>", $transferReceived],
				["<strong class='text-success'>收益活动资金</strong>", $incomeMoneyActive],
				["<strong class='text-success'>收益限制资金</strong>", $incomeMoneyLimit],
				["<strong class='text-success'>收益猫币</strong>", $incomeCoins],
				["<strong class='text-maroon'>投资蛋数</strong>", $investEggs],
				["<strong class='text-maroon'>投资价值</strong>", $investVal],
				["<strong class='text-teal'>交易支款</strong>", $transactionPayed],
				["<strong class='text-teal'>交易收款</strong>", $transactionIncome],
			];

			$table = new Table(null, $rows);
			$box = new Box('统计', $table);

			$tab->add('统计信息', $box->style('info')->solid());

			$content->body($tab);
		});
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

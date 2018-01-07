<?php

namespace App\Admin\Controllers;

use App\Contract;
use App\Http\Controllers\Controller;
use App\IncomeRecord;
use App\InvestRecord;
use App\Nest;
use App\Order;
use App\RechargeApplication;
use App\Supply;
use App\TransactionRecord;
use App\TransferRecord;
use App\User;
use App\WithdrawalApplication;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use League\Flysystem\Config;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('仪表盘');
            $content->description('导航面板');

            $content->row(function (Row $row) {
                $row->column(4, function (Column $column) {
					$users = User::all();
					$infoBox = new InfoBox('用户数量', 'users', 'primary', '/'.config('admin.route.prefix').'/users', count($users));
                    $column->append($infoBox);
                });

                $row->column(4, function (Column $column) {
					$nests = Nest::all();
					$infoBox = new InfoBox('猫窝数量', 'shopping-bag', 'red', '/'.config('admin.route.prefix').'/nests', count($nests));
					$column->append($infoBox);
                });

                $row->column(4, function (Column $column) {
					$rechargeApplications = RechargeApplication::where('status', 'processing')->get();
					$infoBox = new InfoBox('（待处理）充值申请数量', 'dollar', 'green', '/'.config('admin.route.prefix').'/recharge_applications?status=processing', count($rechargeApplications));
					$column->append($infoBox);
                });

				$row->column(4, function (Column $column) {
					$withdrawalApplications = WithdrawalApplication::where('status', 'processing')->get();
					$infoBox = new InfoBox('（待处理）提现申请数量', 'money', 'yellow', '/'.config('admin.route.prefix').'/withdrawal_applications?status=processing', count($withdrawalApplications));
					$column->append($infoBox);
				});

				$row->column(4, function (Column $column) {
					$orders = Order::where('status', 'selling')->get();
					$infoBox = new InfoBox('（在售）市场单数量', 'list-alt', 'purple', '/'.config('admin.route.prefix').'/orders?status=selling', count($orders));
					$column->append($infoBox);
				});

				$row->column(4, function (Column $column) {
					$infoBox = new InfoBox('网站统计', 'bar-chart', 'gray', '/'.config('admin.route.prefix').'/analyse', '-');
					$column->append($infoBox);
				});
            });
        });
    }

    public function analyse()
	{
		return Admin::content(function (Content $content) {
			$content->header('统计');
			$content->description('统计信息');

			$users = User::all();
			$nests = Nest::all();
			$contracts = Contract::all();
			$rechargeApplications = RechargeApplication::all();
			$withdrawalApplications = WithdrawalApplication::all();
			$investRecords = InvestRecord::all();
			$incomeRecords = IncomeRecord::all();
			$transferRecords = TransferRecord::all();
			$transactionRecords = TransactionRecord::all();

			$tab = new Tab();

			// 用户统计
			$moneyActiveSum = $users->sum('money_active');
			$moneyLimitSum = $users->sum('money_limit');
			$moneyCoinsSum = $users->sum('coins');

			$rows = [
				["<strong>总数</strong>", $users->count()],
				["<strong>活动资金总额</strong>", $moneyActiveSum],
				["<strong>限制资金总额</strong>", $moneyLimitSum],
				["<strong>市场资金总额</strong>", $moneyCoinsSum],
			];

			$table = new Table(null, $rows);
			$box = new Box('用户', $table);
			$tab->add('用户统计', $box->style('primary')->solid());

			// 猫窝统计
			$nestsOnSellingCount = $nests->where('is_selling', 1)->count();

			$rows = [
				["<strong>总数</strong>", $nests->count()],
				["<strong>出售中猫窝数</strong>", $nestsOnSellingCount],
			];

			$table = new Table(null, $rows);
			$box = new Box('猫窝', $table);
			$tab->add('猫窝统计', $box->style('danger')->solid());

			// 合约统计
			$contractsFinished = $contracts->where('is_finished', true)->count();
			$contractsLevel1 = $contracts->where('eggs', config('website.CONTRACT_LEVEL_ONE'))->count();
			$contractsLevel2 = $contracts->where('eggs', config('website.CONTRACT_LEVEL_TWO'))->count();
			$contractsLevel3 = $contracts->where('eggs', config('website.CONTRACT_LEVEL_THREE'))->count();
			$contractsLevel4 = $contracts->where('eggs', config('website.CONTRACT_LEVEL_FOUR'))->count();
			$contractsLevel5 = $contracts->where('eggs', config('website.CONTRACT_LEVEL_FIVE'))->count();

			$rows = [
				["<strong>总数</strong>", $contracts->count()],
				["<strong>已完成合约数</strong>", $contractsFinished],
				["<strong>一级合约数</strong>", $contractsLevel1],
				["<strong>二级合约数</strong>", $contractsLevel2],
				["<strong>三级合约数</strong>", $contractsLevel3],
				["<strong>四级合约数</strong>", $contractsLevel4],
				["<strong>五级合约数</strong>", $contractsLevel5],
			];

			$table = new Table(null, $rows);
			$box = new Box('合约', $table);
			$tab->add('合约统计', $box->style('warning')->solid());

			// 提现充值统计
			$rechargeApplicationsAccepted = $rechargeApplications->where('status', 'accepted');
			$withdrawalApplicationsAccepted = $withdrawalApplications->where('status', 'accepted');
			$rechargeMoney = $rechargeApplicationsAccepted->sum('money');
			$withdrawalMoney = $withdrawalApplicationsAccepted->sum('money');

			$rows = [
				["<strong class='text-success'>充值申请总数</strong>", $rechargeApplications->count()],
				["<strong class='text-success'>充值申请接受数</strong>", $rechargeApplicationsAccepted->count()],
				["<strong class='text-success'>充值金额</strong>", $withdrawalMoney],
				["<strong class='text-warning'>提现申请总数</strong>", $withdrawalApplications->count()],
				["<strong class='text-warning'>提现申请接受数</strong>", $withdrawalApplicationsAccepted->count()],
				["<strong class='text-warning'>提现金额</strong>", $withdrawalMoney]
			];

			$table = new Table(null, $rows);
			$box = new Box('充值申请与提现申请', $table);
			$tab->add('充值与提现申请统计', $box->style('success')->solid());

			// 记录统计
			$investEggsSum = $investRecords->sum('eggs');
			$investVal = $investEggsSum * config('website.EGG_VAL');

			$incomeMoneyActive = $incomeRecords->sum('money_active');
			$incomeMoneyLimit = $incomeRecords->sum('money_limit');
			$incomeCoins = $incomeRecords->sum('coins');

			$transferMoney = $transferRecords->sum('money');
			$transactionMoney = $transactionRecords->sum('price');

			$rows = [
				["<strong class='text-purple'>投资记录总数</strong>", $investRecords->count()],
				["<strong class='text-purple'>投资总蛋数</strong>", $investEggsSum],
				["<strong class='text-purple'>投资总价</strong>", $investVal],
				["<strong class='text-success'>收益记录总数</strong>", $incomeRecords->count()],
				["<strong class='text-success'>收益总活动资金</strong>", $incomeMoneyActive],
				["<strong class='text-success'>收益总限制资金</strong>", $incomeMoneyLimit],
				["<strong class='text-success'>收益总猫币</strong>", $incomeCoins],
				["<strong class='text-maroon'>转账记录总数</strong>", $transferRecords->count()],
				["<strong class='text-maroon'>转账总额</strong>", $transferMoney],
				["<strong class='text-teal'>交易记录总数</strong>", $transactionRecords->count()],
				["<strong class='text-teal'>交易总额</strong>", $transactionMoney]
			];

			$table = new Table(null, $rows);
			$box = new Box('记录', $table);
			$tab->add('记录统计', $box->style('default')->solid());

			$content->body($tab);
		});

	}
}

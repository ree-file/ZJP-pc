<?php

namespace App\Admin\Controllers;

use App\Contract;
use App\Http\Controllers\Controller;
use App\Nest;
use App\Order;
use App\Supply;
use App\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Illuminate\Http\Request;
use League\Flysystem\Config;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('主页');
            $content->description('导航面板');

            $content->row(function (Row $row) {
                $row->column(4, function (Column $column) {
					$users = User::all();
					$infoBox = new InfoBox('用户数量', 'users', 'aqua', '/'.config('admin.route.prefix').'/users', count($users));
                    $column->append($infoBox);
                });

                $row->column(4, function (Column $column) {
					$nests = Nest::all();
					$infoBox = new InfoBox('巢数量', 'bitbucket', 'red', '/'.config('admin.route.prefix').'/nests', count($nests));
					$column->append($infoBox);
                });

                $row->column(4, function (Column $column) {
					$supplies = Supply::where('type', 'save')->where('status', 'processing')->get();
					$infoBox = new InfoBox('（待处理）充值申请数量', 'dollar', 'green', '/'.config('admin.route.prefix').'/supplies?type=save&status=processing', count($supplies));
					$column->append($infoBox);
                });

				$row->column(4, function (Column $column) {
					$supplies = Supply::where('type', 'save')->where('status', 'processing')->get();
					$infoBox = new InfoBox('（待处理）提现申请数量', 'money', 'yellow', '/'.config('admin.route.prefix').'/supplies?type=get&status=processing', count($supplies));
					$column->append($infoBox);
				});

				$row->column(4, function (Column $column) {
					$orders = Order::where('status', 'selling')->get();
					$infoBox = new InfoBox('市场在售数量', 'list-alt', 'purple', '/'.config('admin.route.prefix').'/orders?status=selling', count($orders));
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
			$content->description('显示');
			$users = User::all();
			$nests = Nest::all();
			$supplies = Supply::all();
			$contracts = Contract::all();
			$orders = Order::all();
			$tab = new Tab();
			$tab->add('用户统计', view('admin.models.analyse._users', compact('users')));
			$tab->add('申请统计', view('admin.models.analyse._supplies', compact('supplies')));
			$tab->add('巢统计', view('admin.models.analyse._nests', compact('nests')));
			$tab->add('合约统计', view('admin.models.analyse._contracts', compact('contracts')));
			$tab->add('市场统计', view('admin.models.analyse._orders', compact('orders')));
			$content->body($tab);
		});

	}
}

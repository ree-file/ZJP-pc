<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Nest;
use App\Supply;
use App\User;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Http\Request;
use League\Flysystem\Config;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('后台');
            $content->description('统计面板');

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
					$supplies = Supply::all();
					$infoBox = new InfoBox('款项请求数量', 'money', 'green', '/'.config('admin.route.prefix').'/supplies', count($supplies));
					$column->append($infoBox);
                });
            });
        });
    }
}

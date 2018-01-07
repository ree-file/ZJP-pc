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

            $content->header('猫窝');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(Nest::class, function (Grid $grid) {

			// 默认倒序
			$grid->model()->orderBy('id', 'desc');

			// 猫窝信息
			$grid->id('ID')->sortable();
			$grid->name('名字');
			$grid->contracts('合约数')->display(function ($contracts) {
				$count = count($contracts);
				return "<span>{$count}</span>";
			});
			$grid->is_selling('是否在售')->display(function ($boolean) {
				return $boolean ? "<strong class='text-green'>是</strong>" : "<strong>否</strong>";
			})->sortable();
			$grid->price('售价');
			$grid->column('user.email', '窝主邮箱');
            $grid->created_at('创建于')->sortable();

			$grid->actions(function ($actions) {
				$actions->disableDelete();
				$actions->disableEdit();
				$actions->append('<a href="/'.config('admin.route.prefix').'/nests/'.$actions->getKey().'"><i class="fa fa-eye"></i></a>');
			});

			$grid->filter(function($filter){
				// 在这里添加字段过滤器
				$filter->like('name', '名字');
				$filter->equal('user_id', '用户ID');
			});

			// 取消创建
			$grid->disableCreation();
			// 取消批量删除
			$grid->tools(function ($tools) {
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
        });
    }

	public function show($id)
	{
		return Admin::content(function (Content $content) use ($id) {
			$content->header('猫窝');
			$content->description('查看');

			$nest = Nest::withDepth()
				->with(['user', 'contracts', 'incomeRecords', 'investRecords', 'transactionRecords'])
				->find($id);

			$content->row(function ($row) use($nest) {

				$url = '/'.config('admin.route.prefix').'/contracts?nest_id='.$nest->id;
				$count = $nest->contracts->count();
				$row->column(3, new InfoBox('合约', 'calendar-o', 'yellow', $url, $count));

				$url = '/'.config('admin.route.prefix').'/invest_records?nest_id='.$nest->id;
				$count = $nest->investRecords->count();
				$row->column(3, new InfoBox('投资记录', 'cube', 'gray', $url, $count));

				$url = '/'.config('admin.route.prefix').'/income_records?nest_id='.$nest->id;
				$count = $nest->incomeRecords->count();
				$row->column(3, new InfoBox('收益记录', 'cube', 'gray', $url, $count));

				$url = '/'.config('admin.route.prefix').'/transaction_records?nest_id='.$nest->id;
				$count = $nest->transactionRecords->count();
				$row->column(3, new InfoBox('交易记录', 'cube', 'gray', $url, $count));
			});

			$tab = new Tab();

			$userUrl = '/'.config('admin.route.prefix').'/users/'.$nest->user->id;
			$rows = [
				["<strong>ID</strong>", $nest->id],
				["<strong>名字</strong>", $nest->name],
				["<strong>是否在售</strong>", $nest->is_selling ? '<strong class="text-green">是</strong>' : '<strong>否</strong>'],
				["<strong>售价</strong>", $nest->price],
				["<strong>窝主邮箱</strong>", "<a href='{$userUrl}'>{$nest->user->email}</a>"],
				["<strong>创建于</strong>", $nest->created_at],
				["<strong>更新于</strong>", $nest->updated_at]
			];

			$table = new Table(null, $rows);
			$box = new Box('猫窝', $table);
			$tab->add('基本信息', $box->style('danger')->solid());

			// 统计信息
			$descendants = Nest::withDepth()
				->having('depth', '<=', $nest->depth + 20)
				->descendantsOf($nest->id);
			$depth1Count = $descendants->where('depth', $nest->depth + 1)->count();
			$depth2Count = $descendants->where('depth', $nest->depth + 2)->count();
			$depth3Count = $descendants->where('depth', $nest->depth + 3)->count();
			$descendantsCount = $descendants->count();

			$contractsEggsSum = $nest->contracts->sum('eggs');
			$contractsHatchesSum = $nest->contracts->sum('hatches');
			$contractsEggsSumVal = $contractsEggsSum * config('website.EGG_VAL');

			$incomeMoneyActive = $nest->incomeRecords->sum('money_active');
			$incomeMoneyLimit = $nest->incomeRecords->sum('money_limit');
			$incomeCoins = $nest->incomeRecords->sum('money_coins');

			$highestPrice = $nest->transactionRecords->max('price');

			$rows = [
				["<strong class='text-primary'>下一级猫窝数量统计</strong>", $depth1Count],
				["<strong class='text-primary'>下二级猫窝数量统计</strong>", $depth2Count],
				["<strong class='text-primary'>下三级猫窝数量统计</strong>", $depth3Count],
				["<strong class='text-primary'>下二十级猫窝数量统计</strong>", $descendantsCount],
				["<strong class='text-orange'>合约蛋数统计</strong>", $contractsEggsSum],
				["<strong class='text-orange'>合约孵化蛋数统计</strong>", $contractsHatchesSum],
				["<strong class='text-navy'>总投资价值</strong>", $contractsEggsSumVal],
				["<strong class='text-success'>此猫窝活动资金收益</strong>", $incomeMoneyActive],
				["<strong class='text-success'>此猫窝限制资金收益</strong>", $incomeMoneyLimit],
				["<strong class='text-success'>此猫窝猫币收益</strong>", $incomeCoins],
				["<strong class='text-purple'>猫窝最高成交价</strong>", $highestPrice]
			];
			$table = new Table(null, $rows);
			$box = new Box('统计', $table);

			$tab->add('统计信息', $box->style('info')->solid());

			$content->body($tab);
		});
	}
}

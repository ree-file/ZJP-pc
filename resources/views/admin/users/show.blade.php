<div class="tab-pane" id="tab_2">
	<table class="table">
		<thead><tr></tr></thead>
		<tbody>
		<tr>
			<td>总合约数</td>
			<td>{{ count($nest->contracts) }}</td>
		</tr>
		<tr>
			<td>合约累计蛋数</td>
			<td>{{ $nest->contracts->sum('eggs') }}</td>
		</tr>
		<tr>
			<td>邀请人数</td>
			<td>{{ count($nest->receivers) }}</td>
		</tr>
		<tr>
			<td>一级人数</td>
			<td>{{ count($nest->children) }}</td>
		</tr>
		<tr>
			<td>二级人数</td>
			<td>{{ count($grandchildren) }}</td>
		</tr>
		</tbody>
	</table>
</div>
<div class="tab-pane" id="tab_3">
	<table class="table">
		<thead><tr>
			<th>ID</th>
			<th>蛋数</th>
			<th>状态</th>
			<th>周增加</th>
			<th>邀请增加</th>
			<th>社区增加</th>
			<th>已提取（可换为活动资金）</th>
			<th>已提取（可换为限制资金）</th>
			<th>创建于</th>
		</tr></thead>
		<tbody><tr>
			@foreach($contracts as $contract)
				<td>{{ $contract->id }}</td>
				<td>{{ $contract->eggs }}</td>
				<td>{{ $contract->is_finished == true ? '已完成' : '未完成' }}</td>
				<td>{{ $contract->from_weeks }}</td>
				<td>{{ $contract->from_invite }}</td>
				<td>{{ $contract->from_community }}</td>
				<td>{{ $contract->extract_active }}</td>
				<td>{{ $contract->extract_limit }}</td>
				<td>{{ $contract->created_at }}</td>
			@endforeach
		</tr></tbody>
	</table>
</div>
<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">统计信息</a></li>
<li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">合约信息</a></li>
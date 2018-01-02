<table class="table">
	<thead>
	<tr>
		<th>ID</th>
		<th>类型</th>
		<th>金额</th>
		<th>状态</th>
		<th>创建于</th>
	</tr>
	</thead>
	<tbody>
	@foreach($supplies as $supply)
		<tr>
			<td>
				<a href="/{{ config('admin.route.prefix') }}/supplies/{{ $supply->id }}">
					{{ $supply->id }}
				</a>
			</td>
			<td>
				@if ($supply->type == 'save')
					充值
				@else
					提现
				@endif
			</td>
			<td>{{ $supply->money }}</td>
			<td>
				@if ($supply->status == 'accepted')
					<span class="text-success">已接受</span>
				@elseif ($supply->status == 'rejected')
					<span class="text-danger">已拒绝</span>
				@else
					<span class="text-warning">处理中</span>
				@endif</td>
			<td>{{ $supply->created_at }}</td>
		</tr>
	@endforeach
	</tbody>
</table>
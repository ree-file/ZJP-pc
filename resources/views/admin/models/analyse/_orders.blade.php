<table class="table">
	<thead><tr></tr></thead>
	<tbody>
	<tr>
		<td>总市场单数</td>
		<td>{{ count($orders) }}</td>
	</tr>
	<tr>
		<td>已交易数</td>
		<td>{{ count($orders->where('status', 'finished')) }}</td>
	</tr>
	<tr>
		<td>交易总额</td>
		<td>{{ $orders->where('status', 'finished')->sum('price') }}</td>
	</tr>
	</tbody>
</table>
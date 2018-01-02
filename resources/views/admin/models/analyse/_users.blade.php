<table class="table">
	<thead><tr></tr></thead>
	<tbody>
	<tr>
		<td>总人数</td>
		<td>{{ count($users) }}</td>
	</tr>
	<tr>
		<td>活动资金总额</td>
		<td>{{ $users->sum('money_active') }}</td>
	</tr>
	<tr>
		<td>限制资金总额</td>
		<td>{{ $users->sum('money_limit') }}</td>
	</tr>
	<tr>
		<td>市场资金总额</td>
		<td>{{ $users->sum('money_market') }}</td>
	</tr>
	</tbody>
</table>

<table class="table">
	<thead><tr></tr></thead>
	<tbody>
	<tr>
		<td>总请求数</td>
		<td>{{ count($supplies) }}</td>
	</tr>
	<tr>
		<td>充值请求成功数</td>
		<td>{{ count($supplies->where('type', 'save')->where('status', 'accepted')) }}</td>
	</tr>
	<tr>
		<td>充值请求成功总额度</td>
		<td>{{ $supplies->where('type', 'save')->where('status', 'accepted')->sum('money') }}</td>
	</tr>
	<tr>
		<td>提现请求成功数</td>
		<td>{{ count($supplies->where('type', 'get')->where('status', 'accepted')) }}</td>
	</tr>
	<tr>
		<td>提现请求成功总额度</td>
		<td>{{ $supplies->where('type', 'get')->where('status', 'accepted')->sum('money') }}</td>
	</tr>
	</tbody>
</table>
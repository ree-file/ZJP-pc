<table class="table">
	<thead><tr></tr></thead>
	<tbody>
	<tr>
		<td>总合约数</td>
		<td>{{ count($contracts) }}</td>
	</tr>
	<tr>
		<td>已完成合约数</td>
		<td>{{ count($contracts->where('is_finished', 1)) }}</td>
	</tr>
	<tr>
		<td>一级合约数</td>
		<td>{{ count($contracts->where('eggs', config('zjp.CONTRACT_LEVEL_ONE'))) }}</td>
	</tr>
	<tr>
		<td>二级合约数</td>
		<td>{{ count($contracts->where('eggs', config('zjp.CONTRACT_LEVEL_TWO'))) }}</td>
	</tr>
	<tr>
		<td>三级合约数</td>
		<td>{{ count($contracts->where('eggs', config('zjp.CONTRACT_LEVEL_THREE'))) }}</td>
	</tr>
	<tr>
		<td>四级合约数</td>
		<td>{{ count($contracts->where('eggs', config('zjp.CONTRACT_LEVEL_FOUR'))) }}</td>
	</tr>
	<tr>
		<td>五级合约数</td>
		<td>{{ count($contracts->where('eggs', config('zjp.CONTRACT_LEVEL_FIVE'))) }}</td>
	</tr>
	<tr>
		<td>总蛋数</td>
		<td>{{ $contracts->sum('eggs') }}</td>
	</tr>
	<tr>
		<td>总提取额</td>
		<td>{{ $contracts->sum('extracted') }}</td>
	</tr>
	</tbody>
</table>
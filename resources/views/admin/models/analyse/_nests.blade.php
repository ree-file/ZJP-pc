<table class="table">
	<thead><tr></tr></thead>
	<tbody>
	<tr>
		<td>总巢数</td>
		<td>{{ count($nests) }}</td>
	</tr>
	<tr>
		<td>A社区数</td>
		<td>{{ count($nests->where('community', 'A')) }}</td>
	</tr>
	<tr>
		<td>B社区数</td>
		<td>{{ count($nests->where('community', 'B')) }}</td>
	</tr>
	<tr>
		<td>C社区数</td>
		<td>{{ count($nests->where('community', 'C')) }}</td>
	</tr>
	</tbody>
</table>
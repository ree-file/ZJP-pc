<table class="table">
	<thead>
	<tr>
		<th>ID</th>
		<th>名字</th>
		<th>社区</th>
		<th>创建于</th>
	</tr>
	</thead>
	<tbody>
	@foreach($nests as $nest)
		<tr>
			<td>{{ $nest->id }}</td>
			<td>
				<a href="/{{ config('admin.route.prefix') }}/nests/{{ $nest->id }}">
					{{ $nest->name }}
				</a>
			</td>
			<td>{{ $nest->community }}</td>
			<td>{{ $nest->created_at }}</td>
		</tr>
	@endforeach

	</tbody>
</table>
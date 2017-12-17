<div class="box box-info">
	<div class="box-header with-border">
		<h3 class="box-title">查看</h3>

		<div class="box-tools">
			<div class="btn-group pull-right" style="margin-right: 10px">
				<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">基本信息</a></li>
				<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">统计信息</a></li>
				<li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">合约信息</a></li>
				<li class=""><a href="#tab_4" data-toggle="tab" aria-expanded="false">邀请巢</a></li>
				<li class=""><a href="#tab_5" data-toggle="tab" aria-expanded="false">一级巢</a></li>
				<li class=""><a href="#tab_6" data-toggle="tab" aria-expanded="false">二级巢</a></li>
				<li class="pull-right header">
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1">
					<table class="table">
						<thead><tr></tr></thead>
						<tbody>
						<tr>
							<td>ID</td>
							<td>{{ $nest->id }}</td>
						</tr>
						<tr>
							<td>名字</td>
							<td>
								{{ $nest->name }}
							</td>
						</tr>
						<tr>
							<td>用户邮箱</td>
							<td>
								<a href="/{{ config('admin.route.prefix') }}/users/{{ optional($nest->user)->id }}">
									{{ optional($nest->user)->email }}
								</a>
							</td>
						</tr>
						<tr>
							<td>社区</td>
							<td>{{ $nest->community }}</td>
						</tr>
						<tr>
							<td>邀请巢</td>
							<td>
								@if($nest->inviter)
									<a href="/{{ config('admin.route.prefix') }}/nests/{{ optional($nest->inviter)->id }}">
										{{ optional($nest->inviter)->name }}
									</a>
								@endif
							</td>
						</tr>
						<tr>
							<td>上级巢</td>
							<td>
								@if($nest->parent)
									<a href="/{{ config('admin.route.prefix') }}/nests/{{ optional($nest->parent)->id }}">
										{{ optional($nest->parent)->name }}
									</a>
								@endif
							</td>
						</tr>
						</tbody>
					</table>
				</div>
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
							<td>邀请巢数</td>
							<td>{{ count($nest->receivers) }}</td>
						</tr>
						<tr>
							<td>一级巢数</td>
							<td>{{ count($nest->children) }}</td>
						</tr>
						<tr>
							<td>二级巢数</td>
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
								<td>{{ $contract->from_receivers }}</td>
								<td>{{ $contract->from_community }}</td>
								<td>{{ $contract->extracted_active }}</td>
								<td>{{ $contract->extracted_limit }}</td>
								<td>{{ $contract->created_at }}</td>
							@endforeach
						</tr></tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_4">
					<table class="table">
						<thead><tr>
							<th>ID</th>
							<th>名字</th>
							<th>社区</th>
							<th>创建于</th>
						</tr></thead>
						<tbody><tr>
							@foreach($nest->receivers as $receiver)
								<td>{{ $receiver->id }}</td>
								<td>
									<a href="/{{ config('admin.route.prefix') }}/nests/{{ $receiver->id }}">
										{{ $receiver->name }}
									</a>
								</td>
								<td>{{ $receiver->community }}</td>
								<td>{{ $receiver->created_at }}</td>
							@endforeach
						</tr></tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_5">
					<table class="table">
						<thead><tr>
							<th>ID</th>
							<th>名字</th>
							<th>社区</th>
							<th>创建于</th>
						</tr></thead>
						<tbody><tr>
							@foreach($nest->children as $child)
								<td>{{ $child->id }}</td>
								<td>
									<a href="/{{ config('admin.route.prefix') }}/nests/{{ $child->id }}">
										{{ $child->name }}
									</a>
								</td>
								<td>{{ $child->community }}</td>
								<td>{{ $child->created_at }}</td>
							@endforeach
						</tr></tbody>
					</table>
				</div>
				<div class="tab-pane" id="tab_6">
					<table class="table">
						<thead><tr>
							<th>ID</th>
							<th>名字</th>
							<th>社区</th>
							<th>创建于</th>
						</tr></thead>
						<tbody><tr>
							@foreach($grandchildren as $grandchild)
								<td>{{ $grandchild->id }}</td>
								<td>
									<a href="/{{ config('admin.route.prefix') }}/nests/{{ $grandchild->id }}">
										{{ $grandchild->name }}
									</a>
								</td>
								<td>{{ $grandchild->community }}</td>
								<td>{{ $grandchild->created_at }}</td>
							@endforeach
						</tr></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="box-footer">
	</div>
</div>


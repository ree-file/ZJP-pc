<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
			<ul class="nav nav-tabs">

				<li class="active"><a href="#tab_1469135341" data-toggle="tab">基本信息</a></li>
				<li><a href="#tab_1001444038" data-toggle="tab">银行卡</a></li>
				<li><a href="#tab_973742869" data-toggle="tab"><span>233</span></a></li>

				<li class="pull-right header"></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab_1469135341">
					<form method="PUT" action="{{ $url }}" class="form-horizontal" accept-charset="UTF-8" pjax-container="1">
						<div class="box-body fields-group">

							<div class="form-group ">
								<label class="col-sm-2 control-label">ID</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											{{ $user->id }}&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>
							<div class="form-group ">
								<label class="col-sm-2 control-label">邮箱</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											{{ $user->email }}&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>
							<div class="form-group ">
								<label class="col-sm-2 control-label">交易资金</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											{{ $user->money_active }}&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>
							<div class="form-group ">
								<label class="col-sm-2 control-label">激活资金</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											{{ $user->money_limit }}&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>
							<div class="form-group  ">

								<label for="withdrawal_limit" class="col-sm-2 control-label">提现限制</label>

								<div class="col-sm-8">


									<div class="input-group">

										<span class="input-group-addon">$</span>

										<input style="width: 120px; text-align: right;" type="text"
											   id="withdrawal_limit" name="withdrawal_limit"
											   value="{{ $user->withdrawal }}"
											   class="form-control withdrawal_limit" placeholder="输入 提现限制">


									</div>


								</div>
							</div>
							<div class="form-group  ">

								<label for="is_freezed" class="col-sm-2 control-label">是否冻结</label>

								<div class="col-sm-8">


									<div
										class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-small bootstrap-switch-animate"
										style="width: 80px;">
										<div class="bootstrap-switch-container"
											 style="width: 117px; margin-left: -39px;"><span
												class="bootstrap-switch-handle-on bootstrap-switch-primary"
												style="width: 39px;">ON</span><span class="bootstrap-switch-label"
																					style="width: 39px;">&nbsp;</span><span
												class="bootstrap-switch-handle-off bootstrap-switch-default"
												style="width: 39px;">OFF</span><input type="checkbox"
																					  class="is_freezed la_checkbox">
										</div>
									</div>
									<input type="hidden" class="is_freezed" name="is_freezed" value="off">


								</div>
							</div>

							<div class="form-group ">
								<label class="col-sm-2 control-label">创建于</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											2018-01-05 11:00:59&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>
							<div class="form-group ">
								<label class="col-sm-2 control-label">更新于</label>
								<div class="col-sm-8">
									<div class="box box-solid box-default no-margin">
										<!-- /.box-header -->
										<div class="box-body">
											2018-01-05 13:49:47&nbsp;
										</div><!-- /.box-body -->
									</div>


								</div>
							</div>

						</div>

						<!-- /.box-body -->
						<div class="box-footer">
							<input type="hidden" name="_token" value="txQFJuAMW9SDRPG2ei8xllqfKO5r527dAo2OeG4q">
							<div class="col-md-2"></div>

							<div class="col-md-8">
								<div class="btn-group pull-left">
									<button type="reset" class="btn btn-warning pull-right">撤销</button>
								</div>
								<div class="btn-group pull-right">
									<button type="submit" class="btn btn-info pull-right">提交</button>
								</div>

							</div>

						</div>
					</form>
				</div>
				<div class="tab-pane " id="tab_1001444038">
					<div class="box box-solid box-success box-solid box-success">
						<div class="box-header with-border">
							<h3 class="box-title">银行卡列表</h3>
							<div class="box-tools pull-right">
							</div><!-- /.box-tools -->
						</div><!-- /.box-header -->
						<div class="box-body" style="display: block;">
							<table class="table">
								<thead>
								<tr>
									<th>ID</th>
									<th>银行</th>
									<th>账户名</th>
									<th>卡号</th>
									<th>创建于</th>
								</tr>
								</thead>
								<tbody>
								<tr>
									<td>1</td>
									<td>中国银行</td>
									<td>林郑杰</td>
									<td>33323131231</td>
									<td>2018-01-05 16:33:22</td>
								</tr>
								</tbody>
							</table>
						</div><!-- /.box-body -->
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
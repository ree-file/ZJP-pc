<div class="box-header with-border">
	<h3 class="box-title">编辑</h3>

	<div class="box-tools">
		<div class="btn-group pull-right" style="margin-right: 10px">
			<a href="http://zjp.work/admin/supplies" class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;列表</a>
		</div> <div class="btn-group pull-right" style="margin-right: 10px">
			<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;返回</a>
		</div>
	</div>
</div>
<div class="box-body">

	<div class="fields-group">

		<div class="form-group ">
			<label class="col-sm-2 control-label">ID</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						{{ $supplies->id }}
					</div><!-- /.box-body -->
				</div>


			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">用户邮箱</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						{{ $supplies->optional('user')->email }}
					</div><!-- /.box-body -->
				</div>


			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">类型</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						@if ($supplies->type == 'save')
						    充值
						@else
							提款
						@endif
					</div><!-- /.box-body -->
				</div>


			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">状态</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						@if ($supplies->status == 'accepted')
							<span class="text-success">已接受</span>
						@elseif ($supplies->status == 'rejected')
							<span class="text-danger">已拒绝</span>
						@else
							<span class="text-warning">处理中</span>
						@endif
					</div><!-- /.box-body -->
				</div>


			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">金额</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						{{ $supplies->money }}
					</div><!-- /.box-body -->
				</div>


			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">附加信息</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						{{ $supplies->message }}
					</div><!-- /.box-body -->
				</div>
			</div>
		</div>
		<div class="form-group ">
			<label class="col-sm-2 control-label">创建于</label>
			<div class="col-sm-8">
				<div class="box box-solid box-default no-margin">
					<!-- /.box-header -->
					<div class="box-body">
						{{ $supplies->created_at }}
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
						{{ $supplies->updated_at }}
					</div><!-- /.box-body -->
				</div>
			</div>
		</div>
	</div>
</div>
<div class="box-footer">
	<div class="col-md-2">
	</div>
	<div class="col-md-8">

		<div class="btn-group pull-right">
			<button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 提交">提交</button>
		</div>
	</div>

</div>
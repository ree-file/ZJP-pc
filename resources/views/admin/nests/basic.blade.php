<div class="fields-group">

	<div class="form-group ">
		<label class="col-sm-2 control-label">ID</label>
		<div class="col-sm-8">
			<div class="box box-solid box-default no-margin">
				<!-- /.box-header -->
				<div class="box-body">
					{{ $nest->id }}
				</div><!-- /.box-body -->
			</div>
		</div>
	</div>

	<div class="form-group ">
		<label class="col-sm-2 control-label">名字</label>
		<div class="col-sm-8">
			<div class="box box-solid box-default no-margin">
				<!-- /.box-header -->
				<div class="box-body">
					{{ $nest->name }}
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
					{{ $nest->optional('user')->email }}
				</div><!-- /.box-body -->
			</div>


		</div>
	</div>

	<div class="form-group ">
		<label class="col-sm-2 control-label">邀请巢</label>
		<div class="col-sm-8">
			<div class="box box-solid box-default no-margin">
				<!-- /.box-header -->
				<div class="box-body">
					{{ $nest->optional('inviter')->name }}
				</div><!-- /.box-body -->
			</div>


		</div>
	</div>

	<div class="form-group ">
		<label class="col-sm-2 control-label">上级巢</label>
		<div class="col-sm-8">
			<div class="box box-solid box-default no-margin">
				<!-- /.box-header -->
				<div class="box-body">
					{{ $nest->optional('parent')->name }}
				</div><!-- /.box-body -->
			</div>


		</div>
	</div>

	<div class="form-group ">
		<label class="col-sm-2 control-label">社区</label>
		<div class="col-sm-8">
			<div class="box box-solid box-default no-margin">
				<!-- /.box-header -->
				<div class="box-body">
					{{ $nest->community }}
				</div><!-- /.box-body -->
			</div>


		</div>
	</div>
</div>
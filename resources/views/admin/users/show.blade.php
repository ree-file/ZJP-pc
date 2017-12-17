<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#tab-form-1" data-toggle="tab" aria-expanded="true">
				基本信息 <i class="fa fa-exclamation-circle text-red hide"></i>
			</a>
		</li>
		<li class="">
			<a href="#tab-form-2" data-toggle="tab" aria-expanded="false">
				银行卡 <i class="fa fa-exclamation-circle text-red hide"></i>
			</a>
		</li>
		<li class="">
			<a href="#tab-form-3" data-toggle="tab" aria-expanded="false">
				巢 <i class="fa fa-exclamation-circle text-red hide"></i>
			</a>
		</li>
	</ul>
	<div class="tab-content fields-group">

		<div class="tab-pane active" id="tab-form-1">
			<div class="form-group ">
				<label class="col-sm-2 control-label">ID</label>
				<div class="col-sm-8">
					<div class="box box-solid box-default no-margin">
						<!-- /.box-header -->
						<div class="box-body">
							{{ $user->id }}
						</div>
						<!-- /.box-body -->
					</div>
				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label">邮箱</label>
				<div class="col-sm-8">
					<div class="box box-solid box-default no-margin">
						<!-- /.box-header -->
						<div class="box-body">
							{{ $user->email }}
						</div>
						<!-- /.box-body -->
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="tab-form-2">
			<div class="form-group  ">

				<label for="profile_homepage" class="col-sm-2 control-label">Profile homepage</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-internet-explorer"></i></span>

						<input type="url" id="profile_homepage" name="profile[homepage]" value="http://laravel-admin.org" class="form-control profile_homepage_" placeholder="Input Profile homepage">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_last_login_ip" class="col-sm-2 control-label">Profile last login ip</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-laptop"></i></span>

						<input style="width: 130px" type="text" id="profile_last_login_ip" name="profile[last_login_ip]" value="192.30.253.113" class="form-control profile_last_login_ip_" placeholder="Input Profile last login ip">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_last_login_at" class="col-sm-2 control-label">Profile last login at</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>

						<input style="width: 160px" type="text" id="profile_last_login_at" name="profile[last_login_at]" value="" class="form-control profile_last_login_at_" placeholder="Input Profile last login at">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_color" class="col-sm-2 control-label">Profile color</label>

				<div class="col-sm-8">


					<div class="input-group colorpicker-element">

						<span class="input-group-addon"><i style="background-color: rgb(196, 140, 31);"></i></span>

						<input style="width: 140px" type="text" id="profile_color" name="profile[color]" value="#c48c1f" class="form-control profile_color_" placeholder="Input Profile color">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_mobile" class="col-sm-2 control-label">Profile mobile</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-phone"></i></span>

						<input style="width: 150px" type="text" id="profile_mobile" name="profile[mobile]" value="13524120142" class="form-control profile_mobile_" placeholder="Input Profile mobile">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_birthday" class="col-sm-2 control-label">Profile birthday</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>

						<input style="width: 110px" type="text" id="profile_birthday" name="profile[birthday]" value="" class="form-control profile_birthday_" placeholder="Input Profile birthday">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="profile_age" class="col-sm-2 control-label">Age</label>

				<div class="col-sm-8">


					<span class="irs irs-with-grid" id="irs-2"><span class="irs"><span class="irs-line"><span class="irs-line-left"></span><span class="irs-line-mid"></span><span class="irs-line-right"></span></span><span class="irs-min" style="display: block;">20years old</span><span class="irs-max" style="display: block;">50years old</span><span class="irs-from" style="display: none;">0</span><span class="irs-to" style="display: none;">0</span><span class="irs-single" style="left: 0px;">35years old</span><span class="irs-slider single" style="left: -11px;"></span></span><span class="irs-grid"><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol small" style="left: -1px;"></span><span class="irs-grid-pol" style="left: -1px;"></span><span class="irs-grid-text" style="left: -1px; text-align: left;">20</span><span class="irs-grid-pol" style="left: -1px;"></span><span class="irs-grid-text" style="left: -51px;">28</span><span class="irs-grid-pol" style="left: -1px;"></span><span class="irs-grid-text" style="left: -51px;">35</span><span class="irs-grid-pol" style="left: -1px;"></span><span class="irs-grid-text" style="left: -51px;">43</span><span class="irs-grid-pol" style="left: -1px;"></span><span class="irs-grid-text" style="left: -101px; text-align: right;">50</span></span></span><input type="text" class="profile_age_" name="profile[age]" data-from="35" value="35" style="display: none;">
				</div>
			</div>

			<div class="form-group  ">

				<label for="profile_created_at" class="col-sm-2 control-label">Time line</label>

				<div class="col-sm-8">


					<div class="row" style="width: 390px">
						<div class="col-lg-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input type="text" name="profile[created_at]" value="2017-12-11 16:09:17" class="form-control profile_created_at_" style="width: 160px">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								<input type="text" name="profile[updated_at]" value="2017-12-15 09:07:22" class="form-control profile_updated_at_" style="width: 160px">
							</div>
						</div>
					</div>


				</div>
			</div>

		</div>
		<div class="tab-pane" id="tab-form-3">
			<div class="form-group  ">

				<label for="sns_qq" class="col-sm-2 control-label">Sns qq</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_qq" name="sns[qq]" value="" class="form-control sns_qq_" placeholder="Input Sns qq">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_wechat" class="col-sm-2 control-label">Sns wechat</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_wechat" name="sns[wechat]" value="10" class="form-control sns_wechat_" placeholder="Input Sns wechat">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_weibo" class="col-sm-2 control-label">Sns weibo</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_weibo" name="sns[weibo]" value="" class="form-control sns_weibo_" placeholder="Input Sns weibo">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_github" class="col-sm-2 control-label">Sns github</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_github" name="sns[github]" value="" class="form-control sns_github_" placeholder="Input Sns github">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_google" class="col-sm-2 control-label">Sns google</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_google" name="sns[google]" value="" class="form-control sns_google_" placeholder="Input Sns google">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_facebook" class="col-sm-2 control-label">Sns facebook</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_facebook" name="sns[facebook]" value="" class="form-control sns_facebook_" placeholder="Input Sns facebook">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="sns_twitter" class="col-sm-2 control-label">Sns twitter</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="sns_twitter" name="sns[twitter]" value="" class="form-control sns_twitter_" placeholder="Input Sns twitter">
					</div>


				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label">Sns created at</label>
				<div class="col-sm-8">
					<div class="box box-solid box-default no-margin">
						<!-- /.box-header -->
						<div class="box-body">
							2017-09-11 23:17:32&nbsp;
						</div>
						<!-- /.box-body -->
					</div>


				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label">Sns updated at</label>
				<div class="col-sm-8">
					<div class="box box-solid box-default no-margin">
						<!-- /.box-header -->
						<div class="box-body">
							2017-12-11 16:09:19&nbsp;
						</div>
						<!-- /.box-body -->
					</div>


				</div>
			</div>
		</div>
		<div class="tab-pane" id="tab-form-4">
			<div class="form-group  ">

				<label for="address_province_id" class="col-sm-2 control-label">Address province id</label>

				<div class="col-sm-8">


					<input type="hidden" name="address[province_id]"><select class="form-control address_province_id_ select2-hidden-accessible" style="width: 100%;" name="address[province_id]" tabindex="-1" aria-hidden="true"><option value=""></option>
						<option value="2" selected="">北京</option>
						<option value="3">安徽</option>
						<option value="4">福建</option>
						<option value="5">甘肃</option>
						<option value="6">广东</option>
						<option value="7">广西</option>
						<option value="8">贵州</option>
						<option value="9">海南</option>
						<option value="10">河北</option>
						<option value="11">河南</option>
						<option value="12">黑龙江</option>
						<option value="13">湖北</option>
						<option value="14">湖南</option>
						<option value="15">吉林</option>
						<option value="16">江苏</option>
						<option value="17">江西</option>
						<option value="18">辽宁</option>
						<option value="19">内蒙古</option>
						<option value="20">宁夏</option>
						<option value="21">青海</option>
						<option value="22">山东</option>
						<option value="23">山西</option>
						<option value="24">陕西</option>
						<option value="25">上海</option>
						<option value="26">四川</option>
						<option value="27">天津</option>
						<option value="28">西藏</option>
						<option value="29">新疆</option>
						<option value="30">云南</option>
						<option value="31">浙江</option>
						<option value="32">重庆</option>
						<option value="33">香港</option>
						<option value="34">澳门</option>
						<option value="35">台湾</option></select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-addressprovince_id-n2-container"><span class="select2-selection__rendered" id="select2-addressprovince_id-n2-container" title="北京"><span class="select2-selection__clear">×</span>北京</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
				</div>
			</div>

			<div class="form-group  ">

				<label for="address_city_id" class="col-sm-2 control-label">Address city id</label>

				<div class="col-sm-8">


					<input type="hidden" name="address[city_id]"><select class="form-control address_city_id_ select2-hidden-accessible" style="width: 100%;" name="address[city_id]" tabindex="-1" aria-hidden="true"><option value=""></option>
						<option value="52" selected="">北京</option></select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-addresscity_id-oh-container"><span class="select2-selection__rendered" id="select2-addresscity_id-oh-container" title="北京"><span class="select2-selection__clear">×</span>北京</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
				</div>
			</div>

			<div class="form-group  ">

				<label for="address_district_id" class="col-sm-2 control-label">Address district id</label>

				<div class="col-sm-8">


					<input type="hidden" name="address[district_id]"><select class="form-control address_district_id_ select2-hidden-accessible" style="width: 100%;" name="address[district_id]" tabindex="-1" aria-hidden="true"><option value=""></option>
						<option value="500">东城区</option>
						<option value="501">西城区</option>
						<option value="502">海淀区</option>
						<option value="503">朝阳区</option>
						<option value="504">崇文区</option>
						<option value="505" selected="">宣武区</option>
						<option value="506">丰台区</option>
						<option value="507">石景山区</option>
						<option value="508">房山区</option>
						<option value="509">门头沟区</option>
						<option value="510">通州区</option>
						<option value="511">顺义区</option>
						<option value="512">昌平区</option>
						<option value="513">怀柔区</option>
						<option value="514">平谷区</option>
						<option value="515">大兴区</option>
						<option value="516">密云县</option>
						<option value="517">延庆县</option></select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-addressdistrict_id-gs-container"><span class="select2-selection__rendered" id="select2-addressdistrict_id-gs-container" title="宣武区"><span class="select2-selection__clear">×</span>宣武区</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
				</div>
			</div>

			<div class="form-group  ">

				<label for="address_address" class="col-sm-2 control-label">Address address</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>

						<input type="text" id="address_address" name="address[address]" value="" class="form-control address_address_" placeholder="Input Address address">
					</div>


				</div>
			</div>
		</div>
		<div class="tab-pane" id="tab-form-5">
			<div class="form-group  ">

				<label for="password" class="col-sm-2 control-label">Password</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-eye-slash"></i></span>

						<input type="password" id="password" name="password" value="" class="form-control password" placeholder="Input Password">
					</div>


				</div>
			</div>
			<div class="form-group  ">

				<label for="password_confirmation" class="col-sm-2 control-label">Password confirmation</label>

				<div class="col-sm-8">


					<div class="input-group">

						<span class="input-group-addon"><i class="fa fa-eye-slash"></i></span>

						<input type="password" id="password_confirmation" name="password_confirmation" value="" class="form-control password_confirmation" placeholder="Input Password confirmation">
					</div>


				</div>
			</div>
		</div>

	</div>
</div>
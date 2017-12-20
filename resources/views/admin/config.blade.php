<div class="box box-info">
	<div class="box-header with-border">
		<h3 class="box-title">配置</h3>

		<div class="box-tools">
			<div class="btn-group pull-right" style="margin-right: 10px">
				<a class="btn btn-sm btn-default form-history-back"><i class="fa fa-trash"></i>清除缓存生效</a>
			</div>
		</div>
	</div>
	<div class="box-body">
        <form action="/{{ config('admin.route.prefix') }}/config" method="post" accept-charset="UTF-8" role="form">
            {{ csrf_field()}}
            <div class="fields-group col-sm-8 col-sm-offset-2">
                <h1>用户配置</h1>
                <hr/>
                <div class="form-group ">
                    <label class="control-label">最大卡数</label>
                    <input type="number" name="card-max" min="0"
                               value="{{ $config['user']['card-max'] }}" class="form-control" >
                </div>
                <div class="form-group ">
                    <label class="control-label">用户税率（市场转活动资金比例）</label>
                    <input type="number" name="market-to-active" step="0.01" min="0" max="1"
                               value="{{ $config['user']['tax']['market-to-active'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">用户税率（市场交易手续费比例）</label>
                    <input type="number" name="trade" step="0.01" min="0" max="1"
                               value="{{ $config['user']['tax']['trade'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">用户税率（活动资金提现手续费比例）</label>
                    <input type="number" name="active-to-real" step="0.01" min="0" max="1"
                               value="{{ $config['user']['tax']['active-to-real'] }}" class="form-control">
                    <span class="help-block">
                        <i class="fa fa-info-circle"></i>&nbsp;
                        仅仅用于显示，提现时由工作人员自行扣除
                    </span>
                </div>
                <hr/>
                <h1>合约配置</h1>
                <hr/>
                <div class="form-group ">
                    <label class="control-label">蛋的价值（对应美元）</label>
                    <input type="number" name="eggval" min="1"
                               value="{{ $config['contract']['egg']['val'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">一级合约蛋数</label>
                    <input type="number" name="type-0" min="1"
                               value="{{ $config['contract']['type'][0] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">二级合约蛋数</label>
                    <input type="number" name="type-1" min="1"
                               value="{{ $config['contract']['type'][1] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">二级合约蛋数</label>
                    <input type="number" name="type-1" min="1"
                               value="{{ $config['contract']['type'][1] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">三级合约蛋数</label>
                    <input type="number" name="type-2" min="1"
                               value="{{ $config['contract']['type'][2] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">四级合约蛋数</label>
                    <input type="number" name="type-3" min="1"
                               value="{{ $config['contract']['type'][3] }}" class="form-control">
                </div>
                <hr/>
                <h1>合约周转配置</h1>
                <hr/>
                <div class="form-group ">
                    <label class="control-label">周转天数（结算本周固定增加与本周社区增加）</label>
                    <input type="number" name="date" min="1"
                                   value="{{ $config['contract']['cycle']['date'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">周结算社区增加限制比例</label>
                    <input type="number" name="community-limit" step="0.01" min="0" max="1"
                                   value="{{ $config['contract']['cycle']['community-limit'] }}" class="form-control">
                </div>
                <hr/>
                <h1>合约获利配置</h1>
                <hr/>
                <div class="form-group ">
                    <label class="control-label">合约每周获利比例</label>
                    <input type="number" name="week" step="0.01" min="0" max="1"
                                   value="{{ $config['contract']['profit']['week'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">邀请人获利新合约比例</label>
                    <input type="number" name="invite" step="0.01" min="0" max="1"
                                   value="{{ $config['contract']['profit']['invite'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">B社区获利新合约比例</label>
                    <input type="number" name="community-B" step="0.01" min="0" max="1"
                                   value="{{ $config['contract']['profit']['community-B'] }}" class="form-control">
                </div>
                <div class="form-group ">
                    <label class="control-label">C社区获利新合约比例</label>
                    <input type="number" name="community-C" step="0.01" min="0" max="1"
                                   value="{{ $config['contract']['profit']['community-C'] }}" class="form-control">
                </div>
            </div>
            <div class="col-md-8">
                <div class="btn-group pull-right">
                    <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> 提交">提交</button>
                </div>
            </div>
        </form>
	</div>
	<div class="box-footer">
	</div>
</div>


<? require_once getTemplate('admin/header'); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">提示</h3>
    </div>
    <div class="panel-body">
        内部注资为：私人注资非基金持有人；<br>
        外部注资为：客户注资且为基金持有人；
    </div>
</div>
<div class="col-lg-12 margin-1">
    <form class="bs-component" action="<?= $baseRunUri ?>" method="post">
        <input type="hidden" name="token" value="<?= QHASH ?>">
        <input type="hidden" name="submit" value="true">
        <input type="hidden" name="clientId" value="">
        <div class="form-group">
            <label class="control-label">操作方案</label>
            <div>
                <div class="radio">
                    <label>
                        <input type="radio" name="doProject" value="setcash" checked="">
                        产品资金管理
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="doProject" value="edit">
                        更新理财产品（产品信息修改）
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="doProject" value="add">
                        新增理财产品（新增产品）
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="select" class="control-label">产品名称</label>
            <div>
                <select class="form-control" id="projectId" name="projectId">
                    <option value="0">请选择基金产品</option>
                    <? foreach ($baselist as $info) { ?>
                        <option value="<?= $info[control_baseMoney::TABLE_KEY] ?>"><?= $info[control_baseMoney::TABLE_KEYNAME] ?></option>
                    <? } ?>
                </select>
                <span id="loading"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">产品名称</label>
            <div>
                <input type="text" id="projectName" name="projectName" value="" class="form-control">
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="select" class="control-label">注资方案</label>
            <div>
                <select class="form-control" name="cashType">
                    <option value="outcash_in">外部注资</option>
                    <option value="outcash_out">外部撤资</option>
                    <option value="incash_in">内部注资</option>
                    <option value="incash_out">内部撤资</option>
                </select>
            </div>
        </div>
        <? foreach (control_baseMoney::getMoneyArray() as $moneyFeild => $moneyName) { ?>
            <div class="form-group">
                <label class="control-label"><?= $moneyName ?></label>
                <p>￥ <span id="<?= $moneyFeild ?>">0.00</span></p>
            </div>
        <? } ?>
        <div class="form-group">
            <label class="control-label">收益率</label>
            <p><a href="#" id="mtor" class="btn btn-success btn-lg">0%</a></p>
        </div>
        <div class="form-group">
            <label class="control-label">选择客户</label>
            <div>
                <input type="text" name="clientName" value="" data-value="" class="form-control" placeholder="输入客户的名字或者ID" autocomplete="off">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">资金数量</label>
            <div>
                <input type="text" name="clientMoney" value="" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-11 col-lg-offset-1">
                <button type="submit" class="btn btn-primary btn-lg">更新产品与资金信息</button> 
            </div>
        </div>
    </form>
</div>
<div id="logAjax" class="col-lg-12 margin-1">
    
</div>
<script>
    $(document).ready(function () {
        $('#projectId').change(function () {
            getAdminAjax('project', $(this).val(), 'json', function (response) {
                $('#baseMoney').text(response.data.baseMoney);
                $('#runMoney').text(response.data.runMoney);
                $('#mtor').text((100 - ((response.data.baseMoney / response.data.runMoney) * 100)).toFixed(2) + '%');
                $('#projectName').val(response.data.baseName);
            });
            getAdminAjax('projectlog', $(this).val(), 'html', function (response) {
                $('#logAjax').html(response);
            });
        });
        function displayResult(item) {
            $('input[name=clientId]').val(item.value);
        }
        $("input[name=clientName]").typeahead({
            onSelect: displayResult,
            ajax: {
                url: "<?= $ajaxSearchUri ?>",
                timeout: 500,
                triggerLength: 0,
                displayField: "keyName",
                valueField: "key",
                method: "post",
            }
        });
    });
</script>
<? require_once getTemplate('admin/footer'); ?>
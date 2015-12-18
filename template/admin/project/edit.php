<div class="col-lg-12 margin-1">
    <form class="bs-component" action="<?= $projectEditUri ?>" method="post">
        <input type="hidden" name="token" value="<?= QHASH ?>">
        <input type="hidden" name="submit" value="true">
        <input type="hidden" name="baseId" value="<?=$baseId?>">
        <div class="form-group">
            <label class="control-label">产品名称</label>
            <div>
                <input type="text" id="projectName" name="projectName" value="" class="form-control">
                <span class="help-block"></span>
            </div>
        </div>
        <? foreach (control_baseMoney::getMoneyArray() as $moneyFeild => $moneyName) { ?>
            <div class="form-group">
                <label class="control-label"><?= $moneyName ?></label>
                <div class="input-group">
                    <span class="input-group-addon">￥</span>
                    <input name="<?= $moneyFeild ?>" type="text" value="0.00" class="form-control">
                </div>
            </div>
        <? } ?>
        <div class="form-group">
            <label class="control-label">收益率</label>
            <p><a href="#" id="mtor" class="btn btn-success btn-lg">0%</a></p>
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
        getAdminAjax('project', '<?= $baseId ?>', 'json', function (response) {
            $('input[name="baseMoney"]').val(response.data.baseMoney);
            $('input[name="runMoney"]').val(response.data.runMoney);
            $('input[name="mtor"]').text((100 - ((response.data.baseMoney / response.data.runMoney) * 100)).toFixed(2) + '%');
            $('input[name="projectName"]').val(response.data.baseName);
        });
        getAdminAjax('projectlog', '<?= $baseId ?>', 'html', function (response) {
            $('#logAjax').html(response);
        });
    });
</script>
<? require_once getTemplate('admin/header'); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">提示</h3>
    </div>
    <div class="panel-body">
        每周五收益结算；股市资金结算；货币基金结算；
    </div>
</div>
<div class="col-lg-12 margin-1">

    <form class="bs-component" action="<?= $systemRunUri ?>" method="post">
        <input type="hidden" name="token" value="<?= QHASH ?>">
        <input type="hidden" name="submit" value="true">
        <div class="form-group">
            <label class="control-label" for="disabledInput">运行资料更新时间</label>
            <input class="form-control" id="disabledInput" type="text" value="<?= todate($baseInfo[control_systemBase::TABLE_UPDATETIME], 'all') ?>" disabled="">
        </div>
        <? foreach (control_systemBase::getMoneyArray() as $moneyFeild => $moneyName) { ?>
            <div class="form-group">
                <label class="control-label"><?= $moneyName ?></label>
                <div class="input-group">
                    <span class="input-group-addon">￥</span>
                    <input type="text" name="<?= $moneyFeild ?>" value="<?= $baseInfo[$moneyFeild] ?>" class="form-control">
                </div>
            </div>
        <? } ?>
        <div class="form-group">
            <label class="control-label">日期控件</label>
            <div class="input-group">
                <span class="input-group-addon">￥</span>
                <input type='text' class="form-control" id='datetimepicker4' />
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker4').datetimepicker({
                    viewMode: 'years',
                    format: 'YYYY/MM/DD',
                    locale: 'zh-cn'
                });
            });
        </script>
        <div class="form-group">
            <div class="col-lg-11 col-lg-offset-1">
                <button type="submit" class="btn btn-primary btn-lg">更新运行资料</button>
                <a href="#" class="btn btn-success btn-lg">收益率：<?= 100 - round($baseInfo[control_systemBase::TABLE_TODAYCASH] / $baseInfo[control_systemBase::TABLE_TODAYGETCASH], 4) * 100 ?>%</a>
            </div>
        </div>
    </form>
</div>
<div class="col-lg-12 margin-1">
    <table class="table table-hover">
        <tr class="active">
            <th>更新日期</th>
            <th>运行资金</th>
            <th>实资资金</th>
            <th>股市市值</th>
            <th>股市实资</th>
        </tr>
        <? if (!$baseLogList) { ?>
            <tr>
                <td colspan="5"> 暂未有数据 </td>
            </tr>
        <? } ?>
        <? foreach ($baseLogList as $baseVal) { ?>
            <tr>
                <td><?= todate($baseVal['updateTime']) ?></td>
                <td><?= ($baseVal[control_systemBase::TABLE_TODAYGETCASH]) ?></td>
                <td><?= ($baseVal[control_systemBase::TABLE_TODAYCASH]) ?></td>
                <td><?= ($baseVal[control_systemBase::TABLE_RUNNOSTOCK]) ?></td>
                <td><?= ($baseVal[control_systemBase::TABLE_RUNSTOCK]) ?></td>
            </tr>
        <? } ?>
    </table>
</div>
<? require_once getTemplate('admin/footer'); ?>
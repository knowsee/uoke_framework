<div class="col-lg-12 margin-1">
    <table class="table table-hover">
        <tr class="active">
            <th>产品名称</th>
            <th>投资总金额</th>
            <th>投资中金额</th>
            <th>开仓时间</th>
            <th>—</th>
        </tr>
        <? if (!$userPlanList) { ?>
            <tr>
                <td colspan="5"> 暂未有数据 </td>
            </tr>
        <? } ?>
        <? foreach ($userPlanList as $plan) { ?>
            <tr>
                <td><?= $basePlanList[$plan[control_userBase::TABLE_KEY]][control_baseMoney::TABLE_KEYNAME] ?></td>
                <td><?= $plan[control_userBase::TABLE_BASEMONEY] ?></td>
                <td><?= $plan[control_userBase::TABLE_RUNMONEY] ?></td>
                <td><?= todate($plan[control_userBase::TABLE_UPDATETIME]) ?></td>
                <td> —— </td>
            </tr>
        <? } ?>
    </table>
    
    <a href="<?=$clientEditUri?>" class="btn btn-success btn-lg">新增客户</a>
</div>


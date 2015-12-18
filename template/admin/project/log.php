<table class="table table-hover">
    <tr class="active">
        <th>更新日期</th>
        <th>运行资金</th>
        <th>实资资金</th>
        <th>操作对象</th>
    </tr>
    <? if (!$baseLogInfo) { ?>
        <tr>
            <td colspan="4"> 暂未有数据 </td>
        </tr>
    <? } ?>
    <? foreach ($baseLogInfo as $baseVal) { ?>
        <tr>
            <td><?= todate($baseVal[control_baseMoney::TABLE_UPDATETIME]) ?></td>
            <td><?= ($baseVal[control_baseMoney::TABLE_BASEMONEY]) ?></td>
            <td><?= ($baseVal[control_baseMoney::TABLE_RUNMONEY]) ?></td>
            <td><?= ($baseVal[control_baseMoney::TABLE_LOG_MSG]) ?></td>
        </tr>
    <? } ?>
</table>
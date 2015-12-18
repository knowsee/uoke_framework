<div class="col-lg-12 margin-1">
    <table class="table table-hover">
        <tr class="active">
            <th>产品ID</th>
            <th>产品名称</th>
            <th>资金规模</th>
            <th>运行资金规模</th>
            <th>收益率</th>
            <th>—</th>
        </tr>
        <? if (!$projectList) { ?>
            <tr>
                <td colspan="5"> 暂未有数据 </td>
            </tr>
        <? } ?>
        <? foreach ($projectList as $project) { ?>
            <tr>
                <td><?= $project[control_baseMoney::TABLE_KEY] ?></td>
                <td><?= $project[control_baseMoney::TABLE_KEYNAME] ?></td>
                <td><?= $project[control_baseMoney::TABLE_BASEMONEY] ?></td>
                <td><?= $project[control_baseMoney::TABLE_RUNMONEY] ?></td>
                <td><?= round(($project[control_baseMoney::TABLE_RUNMONEY]-$project[control_baseMoney::TABLE_BASEMONEY])/$project[control_baseMoney::TABLE_BASEMONEY], 5) ?>%</td>
                <td>
                    <a href="<?=$projectWatchUri?><?= $project[control_baseMoney::TABLE_KEY] ?>" class="btn btn-success btn-sm">产品管理</a>
                    <a href="<?=$projectClientListUri?><?= $project[control_baseMoney::TABLE_KEY] ?>" class="btn btn-success btn-sm">持仓客户</a>
                </td>
            </tr>
        <? } ?>
    </table>
</div>


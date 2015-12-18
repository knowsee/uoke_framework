<div class="col-lg-12 margin-1">
    <table class="table table-hover">
        <tr class="active">
            <th>客户ID</th>
            <th>客户姓名</th>
            <th>客户持仓规模</th>
        </tr>
        <? if (!$clientList) { ?>
            <tr>
                <td colspan="4"> 暂未有数据 </td>
            </tr>
        <? } ?>
        <? foreach ($clientList as $user) { ?>
            <tr>
                <td><?= $clientInfo[$user[control_userBase::TABLE_USERID]][control_user::TABLE_UNIONKEY] ?></td>
                <td><?= $clientInfo[$user[control_userBase::TABLE_USERID]][control_user::TABLE_REALNAME] ?></td>
                <td><?= $user[control_userBase::TABLE_BASEMONEY] ?></td>
            </tr>
        <? } ?>
    </table>
</div>


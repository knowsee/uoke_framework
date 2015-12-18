<div class="col-lg-12 margin-1">
    <table class="table table-hover">
        <tr class="active">
            <th>客户ID</th>
            <th>客户姓名</th>
            <th>客户创建时间</th>
            <th>客户E-mail</th>
            <th>—</th>
        </tr>
        <? if (!$userList) { ?>
            <tr>
                <td colspan="5"> 暂未有数据 </td>
            </tr>
        <? } ?>
        <? foreach ($userList as $user) { ?>
            <tr>
                <td><?= $user[control_user::TABLE_UNIONKEY] ?></td>
                <td><?= $user[control_user::TABLE_REALNAME] ?></td>
                <td><?= todate($user[control_user::TABLE_REGTIME]) ?></td>
                <td><?= $user[control_user::TABLE_EMAIL] ?></td>
                <td>
                    <a href="<?=$clientEditUri?>&userId=<?= $user[control_user::TABLE_UNIONKEY] ?>" class="btn btn-success btn-sm">编辑信息</a>
                    <a href="<?=$clientProjectUri?>&userId=<?= $user[control_user::TABLE_UNIONKEY] ?>" class="btn btn-success btn-sm">持仓信息</a>
                </td>
            </tr>
        <? } ?>
    </table>
    
    <a href="<?=$clientEditUri?>" class="btn btn-success btn-lg">新增客户</a>
</div>


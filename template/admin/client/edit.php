<div class="col-lg-12 margin-1">
    <form class="bs-component" action="<?= $clientEditUri ?>" method="post">
        <input type="hidden" name="token" value="<?= QHASH ?>">
        <input type="hidden" name="submit" value="true">
        <input type="hidden" name="userId" value="<?=$userId?>">
        <div class="form-group">
            <label class="control-label" for="disabledInput">客户资料更新时间</label>
            <input class="form-control" id="disabledInput" type="text" value="<?= todate($userInfo[control_user::TABLE_UPDATETIME], 'all') ?>" disabled="">
        </div>
        <? foreach (control_user::getUserFelidArray() as $userFeild => $userName) { ?>
            <div class="form-group">
                <label class="control-label"><?= $userName ?></label>
                <input type="text" name="<?= $userFeild ?>" value="<?= $userInfo[$userFeild] ?>" class="form-control">
            </div>
        <? } ?>
        <div class="form-group">
            <div class="col-lg-11 col-lg-offset-1">
                <button type="submit" class="btn btn-primary btn-lg"><?=$formTitle?>资料</button>
            </div>
        </div>
    </form>
</div>
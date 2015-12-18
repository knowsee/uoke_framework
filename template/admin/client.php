<? require_once getTemplate('admin/header'); ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">提示</h3>
    </div>
    <div class="panel-body">
        确保用户资料的准确性
    </div>
</div>
<?if($act == 'list') {?>
<? require_once getTemplate('admin/client/list'); ?>
<?}elseif($act == 'projectInfo') {?>
<? require_once getTemplate('admin/client/project'); ?>
<?} else {?>
<? require_once getTemplate('admin/client/edit'); ?>
<?}?>
<? require_once getTemplate('admin/footer'); ?>
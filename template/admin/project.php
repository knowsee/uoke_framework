<? require_once getTemplate('admin/header'); ?>
<? if ($act == 'list') { ?>
    <? require_once getTemplate('admin/project/list'); ?>
<? } elseif ($act == 'clientlist') { ?>
    <? require_once getTemplate('admin/project/clientlist'); ?>
<? } else { ?>
    <? require_once getTemplate('admin/project/edit'); ?>
<? } ?>
<? require_once getTemplate('admin/footer'); ?>
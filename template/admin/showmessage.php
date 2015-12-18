<? require_once getTemplate('admin/header'); ?>

<div class="bs-component">
    <div class="jumbotron">
        <h2>提示信息</h2>
        <p><?=$message?></p>
        <p><a class="btn btn-primary btn-lg" href="<?=$url?>">正在跳转，请不要关闭浏览器</a></p>
    </div>
</div>
<script>
setTimeout("runUri()",2500);
function runUri() {
    window.location.href = '<?=$url?>';
}
</script>
<? require_once getTemplate('admin/footer'); ?>
<!DOCTYPE html>
<html lang="zh">
    <head>
        <meta charset="utf-8">
        <title>Actual Private Admin CP</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="stylesheet" href="<?= STATIC_URL ?>css/admin/bootstrap.min.css" media="screen">
        
        <link rel="stylesheet" href="<?= STATIC_URL ?>css/admin/custom.min.css">
        <link href="<?= STATIC_URL ?>css/admin/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="<?= STATIC_URL ?>js/admin/html5shiv.js"></script>
          <script src="<?= STATIC_URL ?>js/admin/respond.min.js"></script>
        <![endif]-->
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="<?= STATIC_URL ?>js/admin/moment.min.js"></script>
        <script src="<?= STATIC_URL ?>js/admin/bootstrap.min.js"></script>
        
        <script>
            var baseUri = '<?= CONFIG('siteurl') ?>';
        </script>
        <script src="<?= STATIC_URL ?>js/admin/bootstrap-typeahead.min.js"></script>
        <script src="<?= STATIC_URL ?>js/admin/bootstrap-datetimepicker.js"></script>
        <script src="<?= STATIC_URL ?>js/admin/locales.min.js"></script>
        <script src="<?= STATIC_URL ?>js/admin/custom.js"></script>
        <style>body{font-size: 16px;} .margin-1 {margin-bottom: 15px;}</style>
    <body>
        <div class="container">
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Actual Private</a>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                        <ul class="nav navbar-nav">
                            <li class="<?= ACTIVE(SA, 'admin') ?>"><a href="adminStatus.php">金库管理 <span class="sr-only">(current)</span></a></li>
                            <li class="<?= ACTIVE(SA, 'project') ?>"><a href="adminProject.php?act=list">收益管理</a></li>
                            <li class="dropdown <?= ACTIVE(SA, 'client') ?>">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">客户管理 <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="adminClient.php?act=list">客户列表</a></li>
                                    <li><a href="adminClient.php?act=edit">新增客户</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">客户结算</a></li>
                                </ul>
                            </li>
                        </ul>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="输入客户的关键字">
                            </div>
                            <button type="submit" class="btn btn-default">搜索</button>
                        </form>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#">注销</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-4 hidden-xs">
                    <h4>快捷导航</h4>
                    <div class="list-group table-of-contents">
                        <a class="list-group-item" href="adminStatus.php">基金实资</a>
                        <a class="list-group-item" href="adminStatus.php?act=setCash">注资与撤资</a>
                        <a class="list-group-item" href="adminProject.php?act=list">内部收益管理</a>
                        <a class="list-group-item" href="adminProject.php?act=list">外部收益管理</a>
                        <a class="list-group-item" href="adminClient.php?act=list">客户列表</a>
                        <a class="list-group-item" href="adminStatus.php?act=notice">公告描述</a>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-8">
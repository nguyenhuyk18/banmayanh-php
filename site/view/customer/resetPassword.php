<?php require ABSPATH_SITE . 'layout/header.php' ?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-9">
                <ol class="breadcrumb">
                    <li><a href="<?= get_base_path() ?>/" target="_self">Trang chủ</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Tài khoản</span></li>
                </ol>
            </div>
            <div class="clearfix"></div>
            <?php require ABSPATH_SITE . 'view/customer/sidebarAccount.php' ?>
            <div class="col-md-9 account">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">Reset Password</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <form class="form-reset-password" action="<?= app_url("index.php") ?>?c=customer&a=updatePassword" method="POST"
                            role="form">
                            <input type="hidden" name="token" value="<?= h($token) ?>">

                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary pull-right">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

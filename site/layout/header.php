<!DOCTYPE html>
<html>

<head>
    <script>window.APP_BASE = <?= json_encode(get_base_path()) ?>;</script>
    <title>Lensora Camera - Thiết bị nhiếp ảnh chính hãng</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= h(csrf_token()) ?>">
    <script>window.CSRF_TOKEN = <?= json_encode(csrf_token()) ?>;</script>
    <link rel="shortcut icon" type="image/png" href="<?= get_base_path() ?>/upload/camera-mirrorless.png" />
    <link rel="stylesheet" href="<?= get_domain_site() ?>/public/vendor/fontawesome-free-5.11.2-web/css/all.min.css">
    <link rel="stylesheet" href="<?= get_domain_site() ?>/public/vendor/bootstrap-3.3.7-dist/css/bootstrap.min.css">
    <link rel="stylesheet"
        href="<?= get_domain_site() ?>/public/vendor/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="<?= get_domain_site() ?>/public/vendor/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?= get_domain_site() ?>/public/vendor/star-rating/css/star-rating.min.css">
    <link rel="stylesheet" href="<?= get_domain_site() ?>/public/css/style.css?v=20260929-policy-dropdown">
    <script src="<?= get_domain_site() ?>/public/vendor/jquery.min.js"></script>
    <script src="<?= get_domain_site() ?>/public/vendor/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
    <script type="text/javascript"
        src="<?= get_domain_site() ?>/public/vendor/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>
    <script type="text/javascript" src="<?= get_domain_site() ?>/public/vendor/star-rating/js/star-rating.min.js">
    </script>
    <script src="<?= get_domain_site() ?>/public/vendor/format/number_format.js"></script>
    <script src="<?= get_domain_site() ?>/public/vendor/jquery-validation-1.19.3/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= get_domain_site() ?>/public/js/script.js"></script>
</head>
<?php global $routeName , $a, $routeName , $router, $slugify, $search; ?>
<?php $displayCart = (new CartStorage())->fetch(); ?>

<body>
    <header>
        <!-- use for ajax -->
        <input type="hidden" id="reference" value="">
        <!-- Top Navbar -->
        <div class="top-navbar container-fluid">
            <div class="menu-mb">
                <a href="javascript:void(0)" class="btn-close" onclick="closeMenuMobile()">×</a>
                <a class="<?= $routeName == 'home' ? 'active' : '' ?>" href="<?= $router->generate('home') ?>">Trang
                    chủ</a>
                <a class="<?= in_array($routeName , ['product' , 'productDetail' , 'category']) ? 'active' : '' ?>"
                    href="<?= $router->generate('product') ?>">Sản
                    phẩm</a>
                <a href="<?= app_url('gioi-thieu.html') ?>">Giới thiệu</a>
                <a href="<?= app_url('bai-viet.html') ?>">Bài viết</a>
                <a href="<?= app_url('gio-hang.html') ?>">Giỏ hàng</a>
                <?php if (empty($_SESSION['email'])): ?>
                <a href="javascript:void(0)" class="btn-login"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                <a href="javascript:void(0)" class="btn-register"><i class="fas fa-user-plus"></i> Đăng ký</a>
                <?php else: ?>
                <a href="<?= app_url('index.php?c=customer&a=orders') ?>"><i class="fas fa-box"></i> Đơn hàng của tôi</a>
                <a href="<?= app_url('index.php?c=auth&a=logout') ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                <?php endif; ?>
                <details class="mobile-policy-dropdown" <?= in_array($routeName, ['return', 'payment', 'delivery'], true) ? 'open' : '' ?>>
                    <summary class="<?= in_array($routeName, ['return', 'payment', 'delivery'], true) ? 'active' : '' ?>">Chính sách <i class="fas fa-chevron-down"></i></summary>
                    <a class="<?= $routeName == 'return' ? 'active' : '' ?>" href="<?= $router->generate('return') ?>">Chính sách đổi trả</a>
                    <a class="<?= $routeName == 'payment' ? 'active' : '' ?>" href="<?= $router->generate('payment') ?>">Chính sách thanh toán</a>
                    <a class="<?= $routeName == 'delivery' ? 'active' : '' ?>" href="<?= $router->generate('delivery') ?>">Chính sách giao hàng</a>
                </details>
                <a class="<?= $routeName == 'contact' ? 'active' : ''?>" href="<?= app_url("index.php") ?>?c=contact&a=form">Liên hệ</a>
            </div>
            <div class="row">
                <div class="hidden-lg hidden-md col-sm-2 col-xs-1">
                    <span class="btn-menu-mb" onclick="openMenuMobile()"><i
                            class="glyphicon glyphicon-menu-hamburger"></i></span>
                </div>
                <div class="col-md-6 hidden-sm hidden-xs top-message">
                    <span><i class="fas fa-camera"></i> Thiết bị chính hãng · Tư vấn bởi chuyên gia nhiếp ảnh</span>
                    <ul class="list-inline social-links">
                        <li><a href="https://www.facebook.com/HocLapTrinhWebTaiNha.ThayLoc"><i
                                    class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://twitter.com"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="https://www.pinterest.com/"><i class="fab fa-pinterest"></i></a></li>
                        <li><a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-6 col-sm-10 col-xs-11">
                    <ul class="list-inline pull-right top-right">
                        <li class="account-login">
                            <!-- Đăng nhập xong mới có session email !!! -->
                            <?php if(empty($_SESSION['email'])): ?>
                            <a href="javascript:void(0)" class="btn-register">Đăng Ký</a>
                            <?php else: ?>
                            <!-- đã đăng nhập -->
                            <a href="<?= app_url("index.php") ?>?c=customer&a=orders" class="btn-logout">Đơn hàng của tôi</a>

                            <?php endif; ?>

                        </li>
                        <li>
                            <!-- ch đăng nhập -->
                            <?php if(empty($_SESSION['email'])): ?>
                            <a href="javascript:void(0)" class="btn-login">Đăng Nhập </a>
                            <?php else: ?>
                            <!-- đã đăng nhập -->
                            <a href="javascript:void(0)" class="btn-account dropdown-toggle" data-toggle="dropdown"
                                id="dropdownMenu"><?= h($_SESSION['name']) ?></a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu">
                                <li><a href="<?= get_base_path() ?>/index.php?a=show&c=customer">Thông tin tài khoản</a></li>
                                <li><a href="<?= get_base_path() ?>/index.php?a=shippingDefault&c=customer">Địa chỉ giao hàng</a></li>
                                <li><a href="<?= get_base_path() ?>/index.php?a=orders&c=customer">Đơn hàng của tôi</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?= get_base_path() ?>/index.php?c=auth&a=logout">Thoát</a></li>
                            </ul>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End top navbar -->
        <!-- Header -->
        <div class="container header-main">
            <div class="row header-main-row">
                <!-- LOGO -->
                <div class="col-lg-3 col-md-3 col-sm-7 col-xs-8 logo">
                    <a class="brand-logo" href="<?= $router->generate('home') ?>" aria-label="Lensora Camera - Trang chủ">
                        <span class="brand-mark"><i class="fas fa-camera"></i></span>
                        <span class="brand-copy"><strong>LENSORA</strong><small>CAMERA STORE</small></span>
                    </a>
                </div>
                <div class="col-lg-3 col-md-3 hidden-sm hidden-xs call-action">
                    <div class="header-assurance">
                        <i class="fas fa-shield-alt"></i>
                        <span><strong>Bảo hành uy tín</strong><small>Hỗ trợ kỹ thuật tận tâm</small></span>
                    </div>
                </div>
                <!-- HOTLINE AND SERCH -->
                <div class="col-lg-6 col-md-6 col-sm-5 col-xs-4 hotline-search">
                    <div class="header-contact hidden-sm hidden-xs">
                        <span class="contact-icon"><i class="fas fa-headset"></i></span>
                        <p class="hotline-phone"><span><small>Tư vấn sản phẩm</small><a
                                    href="tel:0932538468">0932 538 468</a></span></p>
                    </div>
                    <form class="header-form" action="<?= get_base_path() ?>/san-pham.html">
                        <div class="input-group">
                            <input type="search" class="form-control search" placeholder="Tìm máy ảnh, ống kính, phụ kiện..."
                                name="search" autocomplete="off"
                                value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                            <div class="input-group-btn">
                                <button class="btn bt-search bg-color" type="submit"><i class="fa fa-search"
                                        style="color:#fff"></i>
                                </button>
                            </div>
                            <!-- <input type="hidden" name="c" value="product"> -->
                            <!-- <input type="hidden" name="a" value="list"> -->
                        </div>
                        <div class="search-result">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End header -->
    </header>
    <!-- NAVBAR DESKTOP-->
    <nav class="navbar navbar-default desktop-menu">
        <div class="container">
            <ul class="nav navbar-nav navbar-left hidden-sm hidden-xs">
                <li class="<?= $routeName == 'home' ? 'active' : '' ?>">
                    <a href="<?= $router->generate('home') ?>">Trang chủ</a>
                </li>
                <li class="<?= in_array($routeName , ['product' , 'productDetail' , 'category']) ? 'active' : '' ?>">
                    <a href="<?= $router->generate('product') ?>">Sản phẩm </a>
                </li>
                <li class="<?= $routeName == 'about' ? 'active' : '' ?>"><a href="<?= app_url('gioi-thieu.html') ?>">Giới thiệu</a></li>
                <li class="<?= in_array($routeName, ['articles','articleDetail'], true) ? 'active' : '' ?>"><a href="<?= app_url('bai-viet.html') ?>">Bài viết</a></li>
                <li class="dropdown policy-dropdown <?= in_array($routeName, ['return', 'payment', 'delivery'], true) ? 'active' : '' ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Chính sách <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="<?= $routeName == 'return' ? 'active' : '' ?>"><a href="<?= $router->generate('return') ?>">Chính sách đổi trả</a></li>
                        <li class="<?= $routeName == 'payment' ? 'active' : '' ?>"><a href="<?= $router->generate('payment') ?>">Chính sách thanh toán</a></li>
                        <li class="<?= $routeName == 'delivery' ? 'active' : '' ?>"><a href="<?= $router->generate('delivery') ?>">Chính sách giao hàng</a></li>
                    </ul>
                </li>
                <li class="<?= $routeName == 'contact' ? 'active' : ''?>"><a
                        href="<?= $router->generate('contact') ?>">Liên hệ</a></li>
            </ul>
            <span class="hidden-lg hidden-md experience">Bắt trọn mọi khoảnh khắc</span>
            <ul class="nav navbar-nav navbar-right">
                <li class="cart"><a href="<?= app_url('gio-hang.html') ?>" title="Giỏ Hàng"><i
							 class="fa fa-shopping-cart"></i> <span class="number-total-product"><?= $displayCart->total_product_number ?></span></a></li>
            </ul>
        </div>
    </nav>
    <?php 
    require ABSPATH_SITE . 'layout/message.php';

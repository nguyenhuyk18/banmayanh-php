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
            <div class="col-md-9 order">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">Đơn hàng của tôi</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <!-- Mỗi đơn hàng -->
                        <?php foreach($orders as $order): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Đơn hàng <a
                                        href="<?= app_url("index.php") ?>?c=customer&a=orderDetail&id=<?= $order->getId() ?>">#<?= $order->getId() ?></a>
                                </h5>
                                <span class="date">
                                    Đặt hàng ngày <?= $order->getCreatedDate() ?></span>
                                <?php if ($order->getPaymentMethod() == 2): ?>
                                <p>Stripe: <?= ($paymentsByOrder[$order->getId()] ?? '') === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?></p>
                                <?php endif; ?>
                                <hr>
                                <?php foreach($order->getOrderItems() as $orderItem): 
                                $product = $orderItem->getProduct();
                                $slug = $slugify->slugify($product->getName());
                                $link = $router->generate('productDetail' , ['slug' => $slug, 'id' => $product->getId()]);
                                ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="<?= get_base_path() ?>/upload/<?= $product->getFeaturedImage() ?>" alt=""
                                            class="img-responsive">
                                    </div>
                                    <div class="col-md-3">
                                        <a class="product-name" href="<?= $link ?>"><?= h($product->getName()) ?></a>
                                    </div>
                                    <div class="col-md-2">
                                        Số lượng: <?= $orderItem->getQty() ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?= h($order->getStatus()->getDescription()) ?>
                                    </div>
                                    <div class="col-md-3">
                                        Giao hàng <?= $order->getDeliveredDate() ?>
                                    </div>
                                </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

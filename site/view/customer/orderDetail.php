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
            <div class="col-md-9 order-info">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">Đơn hàng #<?= $order->getId() ?></h4>
                        <?php if ($stripePayment): ?>
                        <p>Thanh toán Stripe: <strong><?= $stripePayment['status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?></strong></p>
                        <?php if (in_array($stripePayment['status'], ['creating','pending'], true)): ?>
                        <p><a class="btn btn-primary" href="<?= app_url('index.php?c=payment&a=stripeResume&order_id=' . $order->getId()) ?>">Tiếp tục thanh toán</a></p>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="clearfix"></div>
                    <aside class="col-md-7 cart-checkout">
                        <?php foreach($order->getOrderItems() as $orderItem): 
                        
                            $product = $orderItem->getProduct();
                            // $product = $orderItem->getProduct();
                                $slug = $slugify->slugify($product->getName());
                                $link = $router->generate('productDetail' , ['slug' => $slug, 'id' => $product->getId()]);
                            ?>
                        <div class="row">
                            <div class="col-xs-2">
                                <img class="img-responsive" src="<?= get_base_path() ?>/upload/<?= $product->getFeaturedImage() ?>"
                                    alt="<?= h($product->getName()) ?>">
                            </div>
                            <div class="col-xs-7">
                                <a class="product-name" href="<?= $link ?>"><?= h($product->getName()) ?></a>
                                <br>
                                <span><?= $orderItem->getQty() ?></span> x
                                <span><?= formatMoney($orderItem->getUnitPrice())  ?>₫</span>
                            </div>
                            <div class="col-xs-3 text-right">
                                <span><?= formatMoney($orderItem->getTotalPrice()) ?>₫</span>
                            </div>
                        </div>
                        <hr>
                        <?php endforeach; ?>
                        <div class="row">
                            <div class="col-xs-6">
                                Tạm tính
                            </div>
                            <div class="col-xs-6 text-right">
                                <?= formatMoney($order->getSubTotalPrice()) ?>₫
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                Phí vận chuyển
                            </div>
                            <div class="col-xs-6 text-right">
                                <?= formatMoney($order->getShippingFee()) ?>₫
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xs-6">
                                Tổng cộng
                            </div>
                            <div class="col-xs-6 text-right">
                                <?= formatMoney($order->getSubTotalPrice() + $order->getShippingFee()) ?>₫
                            </div>
                        </div>
                    </aside>
                    <div class="ship-checkout col-md-5">
                        <h4>Thông tin giao hàng</h4>
                        <div>
                            Họ và tên: <?= h($order->getShippingFullname()) ?>
                        </div>
                        <div>
                            Số điện thoại: <?= h($order->getShippingMobile()) ?>
                        </div>
                        <div>
                            <?php 
                            $ward = $order->getShippingWard();
                            $district = $ward->getDistrict();
                            $province = $district->getProvince();
                            ?>
                            <?= h($province->getName()) ?>
                        </div>
                        <div>
                            <?= h($district->getName()) ?>
                        </div>
                        <div>
                            <?= h($ward->getName()) ?>
                        </div>
                        <div>
                            <?= h($order->getShippingHousenumberStreet()) ?>
                        </div>
                        <div>
                            Phương thức thanh toán: <?= $order->getPaymentMethod() == 2 ? 'Stripe' : ($order->getPaymentMethod() == 0 ? 'COD' : 'Chuyển khoản') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

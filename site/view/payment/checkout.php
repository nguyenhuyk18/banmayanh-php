<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="<?= get_base_path() ?>/" target="_self">Giỏ hàng</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Thông tin giao hàng</span></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <aside class="col-md-6 cart-checkout">
                <?php 
                
                
                foreach($cart->items as $item): 
                    $slug = $slugify->slugify($item['name']);
                    $link = $router->generate('productDetail' , ['slug' => $slug, 'id' => $item['product_id']]);
                
                ?>
                <div class="row">
                    <div class="col-xs-2">
                        <img class="img-responsive" src="<?= get_base_path() ?>/upload/<?= $item['img'] ?>" alt="<?= $item['name'] ?>">
                    </div>
                    <div class="col-xs-7">
                        <a class="product-name" href="<?= $link ?>"><?= $item['name'] ?></a>
                        <br>
                        <span><?= $item['qty'] ?></span> x <span><?= formatMoney($item['unit_price']) ?>₫</span>
                    </div>
                    <div class="col-xs-3 text-right">
                        <span><?= formatMoney($item['total_price'])  ?>₫</span>
                    </div>
                </div>
                <hr>
                <?php endforeach; ?>
                <div class="row">
                    <div class="col-xs-6">
                        Tạm tính
                    </div>
                    <div class="col-xs-6 text-right">
                        <?= formatMoney($cart->total_price) ?>₫
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        Phí vận chuyển
                    </div>
                    <div class="col-xs-6 text-right">

                        <span class="shipping-fee" data=""><?= formatMoney($shipping_fee) ?>₫</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-6">
                        Tổng cộng
                    </div>
                    <div class="col-xs-6 text-right">
                        <span class="payment-total"
                            data="<?= $cart->total_price ?>"><?= formatMoney($cart->total_price + $shipping_fee) ?>₫</span>
                    </div>
                </div>
            </aside>
            <div class="ship-checkout col-md-6">
                <h4>Thông tin giao hàng</h4>
                <?php if(empty($_SESSION['email'])): ?>
                <div>Bạn đã có tài khoản? <a href="javascript:void(0)" class="btn-login">Đăng Nhập </a></div>
                <br>
                <?php endif; ?>
                <?php if (StripeService::configured()): ?>
                <div class="stripe-checkout-notice"><i class="fab fa-stripe"></i><span><strong>Thanh toán online an toàn</strong><small>Bạn sẽ được chuyển đến trang Checkout bảo mật của Stripe.</small></span></div>
                <?php endif; ?>
                <form action="<?= app_url("index.php") ?>?c=payment&a=order" method="POST">
                    <input type="hidden" name="checkout_token" value="<?= h($_SESSION['checkout_token']) ?>">
                    <?php  require ABSPATH_SITE . 'layout/address.php' ?>
                    <h4>Phương thức thanh toán</h4>
                    <div class="form-group">
                        <label> <input type="radio" name="payment_method" checked="" value="0"> Thanh toán khi giao hàng
                            (COD) </label>
                        <div></div>
                    </div>
                    <div class="form-group">
                        <label><input type="radio" name="payment_method" value="2" <?= StripeService::configured() ? '' : 'disabled' ?>> Thanh toán trực tuyến bằng Stripe (thẻ)</label>
                        <?php if (!StripeService::configured()): ?><div class="text-muted">Stripe chưa được cấu hình trên máy chủ.</div><?php endif; ?>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-sm btn-primary pull-right">Hoàn tất đơn hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php if (StripeService::configured()): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var stripeOption = document.querySelector('input[name="payment_method"][value="2"]');
    if (stripeOption) stripeOption.checked = true;
});
</script>
<?php endif; ?>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

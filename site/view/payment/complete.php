<?php
require ABSPATH_SITE . 'layout/header.php';
$isStripe = $order->getPaymentMethod() == 2;
$isPaid = !$isStripe || (($stripePayment['status'] ?? '') === 'paid');
$paymentLabel = $isStripe ? 'Stripe' : ($order->getPaymentMethod() == 1 ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng');
?>
<main id="maincontent" class="page-main checkout-complete-page">
    <div class="container">
        <section class="checkout-complete-card">
            <div class="checkout-complete-icon <?= $isPaid ? 'is-success' : 'is-pending' ?>">
                <i class="fas <?= $isPaid ? 'fa-check' : 'fa-clock' ?>"></i>
            </div>
            <span class="eyebrow"><?= $isPaid ? 'Đặt hàng thành công' : 'Đang chờ thanh toán' ?></span>
            <h1><?= $isPaid ? 'Cảm ơn bạn đã mua hàng!' : 'Đơn hàng đã được ghi nhận' ?></h1>
            <p class="checkout-complete-lead">
                Mã đơn hàng của bạn là <strong>#<?= $order->getId() ?></strong>.
                <?= $isPaid ? 'Lensora sẽ sớm liên hệ để xác nhận và giao hàng.' : 'Hệ thống sẽ cập nhật ngay khi Stripe xác nhận giao dịch.' ?>
            </p>

            <div class="checkout-summary-grid">
                <div><small>Người nhận</small><strong><?= h($order->getShippingFullname()) ?></strong></div>
                <div><small>Số điện thoại</small><strong><?= h($order->getShippingMobile()) ?></strong></div>
                <div><small>Phương thức thanh toán</small><strong><?= h($paymentLabel) ?></strong></div>
                <div><small>Tổng thanh toán</small><strong><?= formatMoney($order->getSubTotalPrice() + $order->getShippingFee()) ?>₫</strong></div>
                <div><small>Ngày đặt</small><strong><?= h(formatVNDateTime($order->getCreatedDate())) ?></strong></div>
                <div><small>Giao hàng dự kiến</small><strong><?= h(formatVNDate($order->getDeliveredDate())) ?></strong></div>
            </div>

            <div class="checkout-complete-actions">
                <?php if ($isStripe && !$isPaid && in_array($stripePayment['status'] ?? '', ['creating', 'pending'], true)): ?>
                <a class="btn btn-primary" href="<?= app_url('index.php?c=payment&a=stripeResume&order_id=' . $order->getId()) ?>">Tiếp tục thanh toán Stripe</a>
                <?php endif; ?>
                <a class="btn btn-primary" href="<?= app_url('index.php?c=customer&a=orderDetail&id=' . $order->getId()) ?>">Xem chi tiết đơn hàng</a>
                <a class="btn btn-default" href="<?= app_url('san-pham.html') ?>">Tiếp tục mua sắm</a>
            </div>
        </section>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

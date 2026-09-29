<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main id="maincontent" class="page-main contact-page">
    <div class="container">
        <ol class="breadcrumb"><li><a href="<?= app_url() ?>">Trang chủ</a></li><li><span>/</span></li><li class="active"><span>Liên hệ</span></li></ol>
        <header class="contact-header">
            <span class="eyebrow">Lensora Camera</span>
            <h1>Chúng tôi có thể giúp gì cho bạn?</h1>
            <p>Gửi yêu cầu tư vấn, hỗ trợ đơn hàng hoặc thông tin sản phẩm.</p>
        </header>
        <div class="row contact-layout">
            <div class="col-md-7">
                <div class="contact-map-card">
                    <iframe title="Bản đồ Lensora" src="https://www.google.com/maps?q=<?= rawurlencode(SCHOOL_MAP_QUERY) ?>&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="contact-map-caption"><i class="fas fa-map-marker-alt"></i><div><small>Địa chỉ</small><strong><?= h(SHOP_ADDRESS) ?></strong></div></div>
                </div>
                <div class="contact-info-grid">
                    <div><i class="fas fa-phone-alt"></i><span><small>Hotline</small><strong>0909 123 456</strong></span></div>
                    <div><i class="fas fa-envelope"></i><span><small>Email</small><strong><?= h(SHOP_OWNER) ?></strong></span></div>
                    <div><i class="fab fa-stripe"></i><span><small>Thanh toán online</small><strong>Bảo mật qua Stripe</strong></span></div>
                </div>
            </div>
            <div class="col-md-5">
                <section class="contact-form-card">
                    <h2>Gửi tin nhắn</h2>
                    <form class="form-contact" action="#" method="post">
                        <div class="form-group"><label>Họ và tên</label><input type="text" class="form-control" name="fullname" required></div>
                        <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" required></div>
                        <div class="form-group"><label>Số điện thoại</label><input type="tel" class="form-control" name="mobile" required></div>
                        <div class="form-group"><label>Nội dung</label><textarea class="form-control" name="content" rows="6" required></textarea></div>
                        <div class="message alert alert-success" style="display:none"></div>
                        <button type="submit" class="btn btn-primary">Gửi yêu cầu <i class="fas fa-paper-plane"></i></button>
                    </form>
                </section>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

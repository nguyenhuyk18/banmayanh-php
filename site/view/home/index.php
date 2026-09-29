<?php require ABSPATH_SITE . 'layout/header.php' ?>

<main id="maincontent" class="page-main home-page">
    <section class="camera-hero" aria-label="Bộ sưu tập máy ảnh mới">
        <img class="camera-hero-image" src="<?= get_base_path() ?>/upload/camera-hero.png"
            alt="Máy ảnh mirrorless và bộ ống kính chuyên nghiệp">
        <div class="container camera-hero-content">
            <div class="hero-copy">
                <span class="eyebrow">Công nghệ tạo nên cảm xúc</span>
                <h1>Bắt trọn mọi<br><strong>khoảnh khắc</strong></h1>
                <p>Khám phá máy ảnh, ống kính và phụ kiện dành cho mọi hành trình sáng tạo.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary hero-primary" href="<?= app_url('san-pham.html') ?>">Khám phá sản phẩm</a>
                    <a class="hero-link" href="<?= app_url('lien-he.html') ?>">Nhận tư vấn <i
                            class="fas fa-arrow-right"></i></a>
                </div>
                <div class="hero-trust">
                    <span><i class="fas fa-check-circle"></i> Chính hãng</span>
                    <span><i class="fas fa-check-circle"></i> Bảo hành rõ ràng</span>
                </div>
            </div>
        </div>
    </section>

    <section class="top-services container" aria-label="Cam kết dịch vụ">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 item">
                <div class="item-inner">
                    <span class="service-icon"><i class="fas fa-shield-alt"></i></span>
                    <span><strong>Chính hãng 100%</strong><small>Nguồn gốc minh bạch</small></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 item">
                <div class="item-inner">
                    <span class="service-icon"><i class="fas fa-sync-alt"></i></span>
                    <span><strong>Đổi trả 7 ngày</strong><small>Nhanh chóng, thuận tiện</small></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 item">
                <div class="item-inner">
                    <span class="service-icon"><i class="fas fa-truck"></i></span>
                    <span><strong>Giao hàng toàn quốc</strong><small>Đóng gói an toàn</small></span>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 item">
                <div class="item-inner">
                    <span class="service-icon"><i class="fas fa-headset"></i></span>
                    <span><strong>Tư vấn chuyên sâu</strong><small>Đồng hành cùng bạn</small></span>
                </div>
            </div>
        </div>
    </section>

    <section class="category-showcase container">
        <div class="section-heading centered">
            <span class="eyebrow">Danh mục nổi bật</span>
            <h2>Thiết bị cho mọi góc nhìn</h2>
            <p>Từ buổi chụp đầu tiên đến những dự án chuyên nghiệp.</p>
        </div>
        <div class="row">
            <div class="col-sm-4"><a class="category-card category-camera"
                    href="<?= h($categoryLinks[1] ?? app_url('san-pham.html')) ?>">
                    <span class="category-card-icon"><i class="fas fa-camera"></i></span>
                    <span><strong>Máy ảnh</strong><small>Mirrorless · DSLR · Compact</small></span><i
                        class="fas fa-arrow-right"></i>
                </a></div>
            <div class="col-sm-4"><a class="category-card category-lens"
                    href="<?= h($categoryLinks[3] ?? app_url('san-pham.html')) ?>">
                    <span class="category-card-icon"><i class="fas fa-dot-circle"></i></span>
                    <span><strong>Ống kính</strong><small>Prime · Zoom · Telephoto</small></span><i
                        class="fas fa-arrow-right"></i>
                </a></div>
            <div class="col-sm-4"><a class="category-card category-accessory"
                    href="<?= h($categoryLinks[5] ?? app_url('san-pham.html')) ?>">
                    <span class="category-card-icon"><i class="fas fa-video"></i></span>
                    <span><strong>Phụ kiện</strong><small>Tripod · Gimbal · Thẻ nhớ</small></span><i
                        class="fas fa-arrow-right"></i>
                </a></div>
        </div>
    </section>

    <section class="product-section container">
        <div class="section-heading">
            <div><span class="eyebrow">Được lựa chọn nhiều</span>
                <h2>Sản phẩm nổi bật</h2>
            </div>
            <a href="<?= app_url('san-pham.html') ?>">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="row equal product-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-xs-6 col-sm-3"><?php require ABSPATH_SITE . 'layout/product.php' ?></div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="product-section product-section-alt">
        <div class="container">
            <div class="section-heading">
                <div><span class="eyebrow">Vừa cập bến</span>
                    <h2>Sản phẩm mới nhất</h2>
                </div>
                <a href="<?= app_url('san-pham.html') ?>?sort=created-desc">Khám phá ngay <i
                        class="fas fa-arrow-right"></i></a>
            </div>
            <div class="row equal product-grid">
                <?php foreach ($latestProducts as $product): ?>
                <div class="col-xs-6 col-sm-3"><?php require ABSPATH_SITE . 'layout/product.php' ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($bestSellers): ?>
    <section class="product-section container">
        <div class="section-heading">
            <div><span class="eyebrow">Tin dùng bởi cộng đồng</span>
                <h2>Bán chạy nhất</h2>
            </div>
        </div>
        <div class="row equal product-grid">
            <?php foreach ($bestSellers as $product): ?>
            <div class="col-xs-6 col-sm-3"><?php require ABSPATH_SITE . 'layout/product.php'; ?></div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php foreach ($categoryProducts as $categoryProduct):
        if (!$categoryProduct['products']) continue;
        $categoryName = $categoryProduct['categoryName'];
        $products = $categoryProduct['products'];
    ?>
    <section class="product-section container category-product-section">
        <div class="section-heading">
            <div><span class="eyebrow">Chọn theo nhu cầu</span>
                <h2><?= h($categoryName) ?></h2>
            </div>
        </div>
        <div class="row equal product-grid">
            <?php foreach ($products as $product): ?>
            <div class="col-xs-6 col-sm-3"><?php require ABSPATH_SITE . 'layout/product.php' ?></div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endforeach; ?>
</main>

<?php require ABSPATH_SITE . 'layout/footer.php'; ?>
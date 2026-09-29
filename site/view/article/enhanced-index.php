<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main id="maincontent" class="page-main article-page">
    <div class="container">
        <ol class="breadcrumb"><li><a href="<?= app_url() ?>">Trang chủ</a></li><li><span>/</span></li><li class="active"><span>Bài viết</span></li></ol>
        <header class="article-header"><span class="eyebrow">Lensora Journal</span><h1>Kiến thức nhiếp ảnh</h1><p>Mẹo chọn thiết bị và kinh nghiệm sáng tạo cho mọi hành trình.</p></header>
        <?php if (!$articles): ?><div class="empty-state">Chưa có bài viết nào được đăng.</div><?php endif; ?>
        <div class="article-grid">
            <?php foreach ($articles as $article): ?>
            <article class="article-card">
                <span class="article-card-icon"><i class="fas fa-camera-retro"></i></span>
                <small><?= h(formatVNDate($article['created_at'])) ?></small>
                <h2><a href="<?= app_url('bai-viet/' . rawurlencode($article['slug']) . '-' . $article['id'] . '.html') ?>"><?= h($article['title']) ?></a></h2>
                <p><?= h($article['summary']) ?></p>
                <a class="article-link" href="<?= app_url('bai-viet/' . rawurlencode($article['slug']) . '-' . $article['id'] . '.html') ?>">Xem bài viết <i class="fas fa-arrow-right"></i></a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

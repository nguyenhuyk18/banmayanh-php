<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main id="maincontent" class="page-main article-detail-page">
    <div class="container article-detail-container">
        <a class="article-back" href="<?= app_url('bai-viet.html') ?>"><i class="fas fa-arrow-left"></i> Tất cả bài viết</a>
        <article class="article-detail-card">
            <span class="eyebrow">Lensora Journal</span>
            <h1><?= h($article['title']) ?></h1>
            <small><?= h(formatVNDate($article['created_at'])) ?></small>
            <p class="article-summary"><?= h($article['summary']) ?></p>
            <div class="article-content"><?= nl2br(h($article['content'])) ?></div>
        </article>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

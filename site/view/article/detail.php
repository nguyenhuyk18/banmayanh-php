<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main class="container" style="min-height:350px;padding:30px 15px">
<p><a href="<?= app_url('bai-viet.html') ?>">← Bài viết</a></p>
<article><h1><?= h($article['title']) ?></h1>
<small><?= h(substr($article['created_at'], 0, 10)) ?></small>
<p><strong><?= h($article['summary']) ?></strong></p>
<div style="white-space:pre-line"><?= h($article['content']) ?></div>
</article></main>
<?php require ABSPATH_SITE . 'layout/footer.php';

<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main class="container" style="min-height:350px;padding:30px 15px">
<h1>Bài viết</h1>
<?php if (!$articles): ?><p>Chưa có bài viết nào được đăng.</p><?php endif; ?>
<?php foreach ($articles as $article): ?>
<article style="border-bottom:1px solid #ddd;padding:15px 0">
<h2><a href="<?= app_url('bai-viet/' . rawurlencode($article['slug']) . '-' . $article['id'] . '.html') ?>"><?= h($article['title']) ?></a></h2>
<small><?= h(substr($article['created_at'], 0, 10)) ?></small>
<p><?= h($article['summary']) ?></p>
</article>
<?php endforeach; ?>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php';

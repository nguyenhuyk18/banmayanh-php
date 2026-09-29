<?php include 'layout/header.php'; ?>
<div id="content-wrapper"><div class="container-fluid">
<h1><?= h($page_title) ?></h1>
<form method="post" action="index.php?c=article&a=<?= $article ? 'update' : 'save' ?>">
<?php if ($article): ?><input type="hidden" name="id" value="<?= $article['id'] ?>"><?php endif; ?>
<div class="form-group"><label for="article-title">Tiêu đề</label><input id="article-title" class="form-control" name="title" required maxlength="200" value="<?= h($article['title'] ?? '') ?>"></div>
<div class="form-group"><label for="article-summary">Tóm tắt</label><textarea id="article-summary" class="form-control" name="summary" required maxlength="500" rows="3"><?= h($article['summary'] ?? '') ?></textarea></div>
<div class="form-group"><label for="article-content">Nội dung</label><textarea id="article-content" class="form-control" name="content" required rows="14"><?= h($article['content'] ?? '') ?></textarea></div>
<div class="form-group"><label><input type="checkbox" name="is_published" value="1" <?= !empty($article['is_published']) ? 'checked' : '' ?>> Công khai bài viết</label></div>
<button class="btn btn-primary" type="submit">Lưu bài viết</button> <a href="index.php?c=article&a=list">Hủy</a>
</form></div></div>
<?php include 'layout/footer.php';

<?php include 'layout/header.php'; ?>
<div id="content-wrapper"><div class="container-fluid">
<h1>Bài viết</h1><p><a class="btn btn-primary" href="index.php?c=article&a=add">Thêm bài viết</a></p>
<div class="table-responsive"><table class="table table-hover" id="dataTable"><thead><tr><th>Tiêu đề</th><th>Trạng thái</th><th>Ngày tạo</th><th></th></tr></thead><tbody>
<?php foreach ($articles as $article): ?>
<tr><td><?= h($article['title']) ?></td><td><?= $article['is_published'] ? 'Đã đăng' : 'Bản nháp' ?></td><td><?= h($article['created_at']) ?></td>
<td><a class="btn btn-warning btn-sm" href="index.php?c=article&a=edit&id=<?= $article['id'] ?>">Sửa</a>
<a class="btn btn-danger btn-sm" href="index.php?c=article&a=delete&id=<?= $article['id'] ?>" onclick="return confirm('Xóa bài viết này?')">Xóa</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php include 'layout/footer.php';

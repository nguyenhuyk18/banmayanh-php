<?php include 'layout/header.php'; ?>
<div id="content-wrapper"><div class="container-fluid">
<h1>Khách hàng</h1>
<p><a class="btn btn-primary" href="index.php?c=customer&a=add">Thêm khách hàng</a></p>
<div class="table-responsive"><table class="table table-hover" id="dataTable">
<thead><tr><th>Tên</th><th>Email</th><th>Điện thoại</th><th>Địa chỉ</th><th>Trạng thái</th><th></th></tr></thead><tbody>
<?php foreach ($customers as $customer): ?>
<tr><td><?= h($customer->getName()) ?></td><td><?= h($customer->getEmail()) ?></td><td><?= h($customer->getMobile()) ?></td>
<td><?= h($customer->getAddress()) ?></td><td><?= $customer->getIsActive() ? 'Đã kích hoạt' : 'Chưa kích hoạt' ?></td>
<td><a class="btn btn-warning btn-sm" href="index.php?c=customer&a=edit&id=<?= $customer->getId() ?>">Sửa</a>
<a class="btn btn-danger btn-sm" href="index.php?c=customer&a=delete&id=<?= $customer->getId() ?>" onclick="return confirm('Xóa khách hàng này?')">Xóa</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<?php include 'layout/footer.php'; ?>

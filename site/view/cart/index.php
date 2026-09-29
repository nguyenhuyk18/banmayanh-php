<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main class="container" style="min-height:380px;padding:30px 15px">
<h1>Giỏ hàng</h1>
<?php if (!$cart->items): ?>
<p>Giỏ hàng của bạn đang trống.</p>
<p><a class="btn btn-primary" href="<?= app_url('san-pham.html') ?>">Xem sản phẩm</a></p>
<?php else: ?>
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>Sản phẩm</th><th>Đơn giá</th><th>Số lượng</th><th>Thành tiền</th><th></th></tr></thead><tbody>
<?php foreach ($cart->items as $item): ?>
<tr>
<td><a href="<?= h($item['url']) ?>"><img src="<?= app_url('upload/' . rawurlencode($item['img'])) ?>" alt="" style="width:65px;max-height:65px;object-fit:contain"> <?= h($item['name']) ?></a></td>
<td><?= formatMoney($item['unit_price']) ?>₫</td>
<td><form method="post" action="<?= app_url('index.php?c=cart&a=update') ?>" class="form-inline">
<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
<input type="number" class="form-control input-sm" name="qty" min="1" value="<?= $item['qty'] ?>" style="width:75px" required>
<button type="submit" class="btn btn-default btn-sm">Cập nhật</button></form></td>
<td><?= formatMoney($item['total_price']) ?>₫</td>
<td><form method="post" action="<?= app_url('index.php?c=cart&a=delete') ?>">
<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
<button type="submit" class="btn btn-danger btn-sm">Xóa</button></form></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<p class="text-right"><strong>Tạm tính: <?= formatMoney($cart->total_price) ?>₫</strong></p>
<p class="text-right"><a href="<?= app_url('san-pham.html') ?>" class="btn btn-default">Tiếp tục mua sắm</a>
<a href="<?= app_url('index.php?c=payment&a=checkout') ?>" class="btn btn-primary">Đặt hàng</a></p>
<?php endif; ?>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

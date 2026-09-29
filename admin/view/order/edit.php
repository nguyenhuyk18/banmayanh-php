<?php include 'layout/header.php'; ?>
<div id="content-wrapper"><div class="container-fluid">
<h1>Xử lý đơn #<?= $order->getId() ?></h1>
<p>Khách hàng: <?= h($order->getCustomer()->getName()) ?></p>
<p>Tổng: <?= formatMoney($order->getSubTotalPrice() + $order->getShippingFee()) ?>₫</p>
<?php if ($payment): ?><p>Stripe: <strong><?= h($payment['status']) ?></strong></p><?php endif; ?>
<form method="post" action="index.php?c=order&a=update">
<input type="hidden" name="order_id" value="<?= $order->getId() ?>">
<div class="form-group"><label>Trạng thái</label><select class="form-control" name="status">
<?php foreach ($statuses as $status): ?><option value="<?= $status->getId() ?>" <?= $order->getStatusId() == $status->getId() ? 'selected' : '' ?>><?= h($status->getDescription()) ?></option><?php endforeach; ?>
</select></div>
<div class="form-group"><label>Nhân viên phụ trách</label><select class="form-control" name="staff_id"><option value="">Chưa phân công</option>
<?php foreach ($staffs as $staff): ?><option value="<?= $staff->getId() ?>" <?= $order->getStaffId() == $staff->getId() ? 'selected' : '' ?>><?= h($staff->getName()) ?></option><?php endforeach; ?>
</select></div>
<button class="btn btn-primary" type="submit">Lưu</button> <a href="index.php?c=order&a=list">Quay lại</a>
</form></div></div>
<?php include 'layout/footer.php';

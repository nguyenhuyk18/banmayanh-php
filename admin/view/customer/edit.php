<?php include 'layout/header.php'; ?>
<div id="content-wrapper"><div class="container-fluid">
<h1>Cập nhật khách hàng</h1>
<form method="post" action="index.php?c=customer&a=update">
<input type="hidden" name="id" value="<?= $customer->getId() ?>">
<div class="form-group"><label>Họ tên<input class="form-control" name="fullname" required value="<?= h($customer->getName()) ?>"></label></div>
<div class="form-group"><label>Email<input class="form-control" name="email" type="email" required value="<?= h($customer->getEmail()) ?>"></label></div>
<div class="form-group"><label>Điện thoại<input class="form-control" name="mobile" required value="<?= h($customer->getMobile()) ?>"></label></div>
<div class="form-group"><label>Mật khẩu mới (để trống nếu giữ nguyên)<input class="form-control" name="password" type="password"></label></div>
<div class="form-group"><label>Tên người nhận<input class="form-control" name="shipping_name" value="<?= h($customer->getShippingName()) ?>"></label></div>
<div class="form-group"><label>Điện thoại người nhận<input class="form-control" name="shipping_mobile" value="<?= h($customer->getShippingMobile()) ?>"></label></div>
<?php include 'layout/address_variable.php'; include 'layout/address_layout.php'; ?>
<div class="form-group"><label>Số nhà, đường<input class="form-control" name="housenumber_street" value="<?= h($customer->getHousenumberStreet()) ?>"></label></div>
<div class="form-group"><label><input type="checkbox" name="active" value="1" <?= $customer->getIsActive() ? 'checked' : '' ?>> Đã kích hoạt</label></div>
<button class="btn btn-primary" type="submit">Lưu</button> <a href="index.php?c=customer&a=list">Quay lại</a>
</form></div></div>
<?php include 'layout/footer.php'; ?>

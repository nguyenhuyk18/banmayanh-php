<?php require ABSPATH_SITE . 'layout/header.php'; ?>
<main id="maincontent" class="page-main policy-page">
    <div class="container">
        <ol class="breadcrumb"><li><a href="<?= app_url() ?>">Trang chủ</a></li><li><span>/</span></li><li class="active"><span>Chính sách thanh toán</span></li></ol>
        <header class="policy-header"><span class="eyebrow">Thanh toán an toàn</span><h1>Chính sách thanh toán</h1><p>Stripe là phương thức thanh toán online chính của Lensora.</p></header>
        <div class="table-responsive policy-table-wrap"><table class="table policy-table">
            <thead><tr><th>Phương thức</th><th>Cách thức</th><th>Trạng thái đơn</th></tr></thead>
            <tbody>
                <tr><th><i class="fab fa-stripe"></i> Stripe</th><td>Thanh toán bằng thẻ trên trang Checkout bảo mật của Stripe. Lensora không lưu thông tin thẻ.</td><td>Xác nhận tự động sau khi giao dịch thành công.</td></tr>
                <tr><th>COD</th><td>Thanh toán cho nhân viên giao hàng khi nhận sản phẩm.</td><td>Đơn được xác nhận trước khi giao.</td></tr>
                <tr><th>Hoàn tiền</th><td>Khoản hoàn được gửi về đúng phương thức đã thanh toán sau khi yêu cầu được duyệt.</td><td>Thời gian hiển thị phụ thuộc ngân hàng phát hành.</td></tr>
            </tbody>
        </table></div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'; ?>

<!-- FOOTER -->
<footer class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12 list footer-brand-column">
                        <a class="brand-logo footer-brand" href="<?= app_url() ?>">
                            <span class="brand-mark"><i class="fas fa-camera"></i></span>
                            <span class="brand-copy"><strong>LENSORA</strong><small>CAMERA STORE</small></span>
                        </a>
                        <p>Nơi thiết bị và cảm hứng gặp nhau. Đồng hành cùng bạn trên mọi hành trình sáng tạo.</p>
                        <ul class="list-inline footer-social">
                            <li><a href="https://www.facebook.com/hoaimeow.nhinhdep.103" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-6 list">
                        <div class="footerLink">
                            <h4>Danh mục</h4>
                            <ul class="list-unstyled">
                                <li><a href="<?= app_url('san-pham.html') ?>">Máy ảnh Mirrorless</a></li>
                                <li><a href="<?= app_url('san-pham.html') ?>">Máy ảnh DSLR</a></li>
                                <li><a href="<?= app_url('san-pham.html') ?>">Ống kính</a></li>
                                <li><a href="<?= app_url('san-pham.html') ?>">Action Camera</a></li>
                                <li><a href="<?= app_url('san-pham.html') ?>">Phụ kiện</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-6 list">
                        <div class="footerLink">
                            <h4>Liên kết </h4>
                            <ul class="list-unstyled">
                                <li><a href="<?= app_url("san-pham.html") ?>">Sản phẩm </a></li>
                                <li><a href="<?= app_url("chinh-sach-doi-tra.html") ?>">Chính sách đổi trả</a></li>
                                <li><a href="<?= app_url("chinh-sach-thanh-toan.html") ?>">Chính sách thanh toán</a></li>
                                <li><a href="<?= app_url("chinh-sach-giao-hang.html") ?>">Chính sách giao hàng </a></li>
                                <li><a href="<?= app_url("lien-he.html") ?>">Liên hệ </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12 list">
                        <div class="footerLink">
                            <h4>Liên hệ với chúng tôi </h4>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-phone-alt"></i> 0932 538 468</li>
                                <li><a href="mailto:hello@lensora.vn"><i class="fas fa-envelope"></i> hello@lensora.vn</a></li>
                                <li><i class="fas fa-clock"></i> 08:00 - 21:00, Thứ 2 - CN</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 list">
                        <div class="newsletter clearfix">
                            <h4>Bản tin</h4>
                            <p>Nhận tin sản phẩm mới, workshop và ưu đãi dành riêng cho người yêu nhiếp ảnh.</p>
                            <form action="<?= app_url("index.php?c=contact&a=subscribe") ?>" method="POST">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Nhập email của bạn.."
                                        name="email">
                                    <button type="submit" class="btn btn-primary send pull-right">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->
<!-- BACK TO TOP -->
<div class="back-to-top" class="bg-color">▲</div>
<!-- END BACK TO TOP -->
<!-- REGISTER DIALOG -->
<div class="modal fade" id="modal-register" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-color">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title text-center">Đăng ký</h3>
            </div>
            <form action="<?= app_url("index.php") ?>?a=register&c=customer" method="POST" role="form" class='form-register'>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="fullname" placeholder="Họ và tên">
                    </div>
                    <div class="form-group">
                        <input type="tel" class="form-control" name="mobile" placeholder="Số điện thoại">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Mật khẩu">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Nhập lại mật khẩu">
                    </div>
                    <input type="hidden" name="reference" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END REGISTER DIALOG -->
<!-- LOGIN DIALOG -->
<div class="modal fade" id="modal-login" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-color">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title text-center">Đăng nhập</h3>
            </div>
            <form action="<?= app_url("index.php") ?>?c=auth&a=login" method="POST" role="form" class="form-login">

                <div class="modal-body">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Đăng Nhập</button><br>
                    <div class="text-left">
                        <a href="javascript:void(0)" class="btn-register">Bạn chưa là thành viên? Đăng kí ngay!</a>
                        <a href="javascript:void(0)" class="btn-forgot-password">Quên Mật Khẩu?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END LOGIN DIALOG -->
<!-- FORTGOT PASSWORD DIALOG -->
<div class="modal fade" id="modal-forgot-password" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-color">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title text-center">Quên mật khẩu</h3>
            </div>
            <form action="<?= app_url("index.php") ?>?c=customer&a=forgotPassword" method="POST" role="form" class="form-forgot-password">
                <div class="modal-body">
                    <div class="form-group">
                        <input name="email" type="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="reference" value="">
                    <button type="submit" class="btn btn-primary">GỬI</button><br>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END FORTGOT PASSWORD DIALOG -->
<!-- CART DIALOG -->
<div class="modal fade" id="modal-cart-detail" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-color">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 class="modal-title text-center">Giỏ hàng</h3>
            </div>
            <div class="modal-body">
                <div class="page-content">
                    <div class="clearfix hidden-sm hidden-xs">
                        <div class="col-xs-1">
                        </div>
                        <div class="col-xs-3">
                            <div class="header">
                                Sản phẩm
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="header">
                                Đơn giá
                            </div>
                        </div>
                        <div class="label_item col-xs-3">
                            <div class="header">
                                Số lượng
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="header">
                                Thành tiền
                            </div>
                        </div>
                        <div class="lcol-xs-1">
                        </div>
                    </div>
                    <div class="cart-product">
						<?php foreach ($displayCart->items as $item): ?>
						<div class="row" style="padding:10px 0;border-bottom:1px solid #ddd">
							<div class="col-xs-2"><img class="img-responsive" src="<?= app_url('upload/' . rawurlencode($item['img'])) ?>" alt=""></div>
							<div class="col-xs-3"><a href="<?= h($item['url']) ?>"><?= h($item['name']) ?></a></div>
							<div class="col-xs-2"><?= formatMoney($item['unit_price']) ?>₫</div>
							<div class="col-xs-3">
								<form method="post" action="<?= app_url('index.php?c=cart&a=update') ?>">
									<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
									<input type="number" name="qty" min="1" value="<?= $item['qty'] ?>" class="form-control input-sm" style="width:65px;display:inline">
									<button type="submit" class="btn btn-default btn-xs">Sửa</button>
								</form>
							</div>
							<div class="col-xs-2"><?= formatMoney($item['total_price']) ?>₫
								<form method="post" action="<?= app_url('index.php?c=cart&a=delete') ?>" style="display:inline">
									<input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
									<button type="submit" class="btn btn-link btn-xs" title="Xóa">×</button>
								</form>
							</div>
						</div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="clearfix">
                    <div class="col-xs-12 text-right">
                        <p>
                            <span>Tổng tiền</span>
                            <span class="price-total"><?= formatMoney($displayCart->total_price) ?>₫</span>
                        </p>
                        <a href="<?= app_url('san-pham.html') ?>" class="btn btn-default">Tiếp tục mua sắm</a>
                        <a href="<?= app_url('index.php?c=payment&a=checkout') ?>" class="btn btn-primary">Đặt hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CART DIALOG -->
<!-- Facebook Messenger Chat -->
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
    FB.init({
        xfbml: true,
        version: 'v4.0'
    });
};

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<!-- Your customer chat code -->
<div class="fb-customerchat" attribution=setup_tool page_id="112296576811987"
    logged_in_greeting="Chào bạn, Lensora có thể tư vấn thiết bị nào cho bạn?"
    logged_out_greeting="Chào bạn, Lensora có thể tư vấn thiết bị nào cho bạn?">
</div>
<!-- End Facebook Messenger Chat -->
</body>

</html>

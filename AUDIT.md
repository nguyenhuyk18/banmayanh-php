# Đối chiếu chức năng bài tập lớn

| Mục | Hiện trạng trên XAMPP | Cách kiểm tra |
|---|---|---|
| 1. Chạy trên internet | Chưa triển khai công khai; hiện chạy trên XAMPP localhost | Xem `SETUP.md` để chuẩn bị HTTPS, domain và webhook khi triển khai |
| 2. Trang chủ, menu, phân loại, bán chạy | Đã có | Trang chủ hiển thị sản phẩm nổi bật, mới nhất, theo danh mục và bán chạy tính từ chi tiết đơn không bị hủy |
| 3. Giới thiệu/bài viết | Đã có | `/gioi-thieu.html`, `/bai-viet.html`; admin tạo, sửa, đăng và xóa bài viết |
| 4. Chi tiết sản phẩm | Đã có | `/san-pham/{slug}-{id}.html` |
| 5. Giỏ hàng, đặt hàng | Đã có | Trang `/gio-hang.html`; form thêm/xóa/sửa giỏ hoạt động kể cả khi JavaScript không chạy; đặt hàng lưu giao dịch và trừ tồn kho |
| 6. Thanh toán trực tuyến | Stripe **test mode** đã tích hợp, thử từ XAMPP tạo Checkout Session thật và hủy phiên qua admin với tồn kho được hoàn lại. Chưa thử một lần trả tiền bằng thẻ trên giao diện Stripe. | Xem `SETUP.md` để thử thẻ test; Stripe CLI listener chuyển webhook về XAMPP. Khi khởi động lại máy cần chạy listener lại. |
| 7. Đăng nhập cho chức năng quan trọng | Đã có | Checkout, thông tin tài khoản và đơn hàng yêu cầu đăng nhập; khách không thể xem đơn của người khác; admin có đăng nhập và phân quyền |
| 8. Post sản phẩm, bài viết | Đã có | Admin thêm/sửa/xóa sản phẩm, tải ảnh, tạo/sửa/đăng/xóa bài viết |
| 9. Xử lý đơn hàng | Đã có | Admin tạo, xác nhận, chuyển trạng thái, hủy/xóa đơn; tồn kho được hoàn khi hủy; đơn Stripe chưa thanh toán không thể xác nhận |
| 10. Thống kê đơn hàng | Đã có | Dashboard admin lọc ngày, đếm đơn, doanh thu loại đơn hủy và đơn Stripe chưa thanh toán |

Kiểm tra tự động: `python tools/smoke.py`, `python tools/stripe_web_smoke.py`, `php tools/stripe_signature_test.php`. Các script tạo dữ liệu thử rồi xóa; Stripe web smoke tạo và hủy phiên Checkout test qua Stripe API. Chưa thể xác nhận mục 1 chỉ bằng localhost.

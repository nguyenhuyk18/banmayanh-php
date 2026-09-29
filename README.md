# Lensora – Website bán máy ảnh PHP

Website thương mại điện tử bán máy ảnh và phụ kiện, viết bằng PHP thuần, MySQL và Bootstrap. Dự án hỗ trợ giỏ hàng, đặt hàng, quản trị sản phẩm/đơn hàng, đăng nhập Google, gửi email và thanh toán Stripe Checkout ở chế độ thử nghiệm.

## Yêu cầu

- Docker Desktop và Docker Compose
- Git
- Cổng `80`, `443`, `3307` và `8081` chưa bị ứng dụng khác sử dụng

## Chạy nhanh bằng Docker

```powershell
git clone https://github.com/nguyenhuyk18/banmayanh-php.git
cd banmayanh-php
Copy-Item .env.example .env
docker compose up -d --build
```

Sau khi các container khởi động:

- Website: [http://localhost](http://localhost)
- Trang quản trị: [http://localhost/admin/login.php](http://localhost/admin/login.php)
- phpMyAdmin: [http://localhost:8081](http://localhost:8081)
- MySQL từ máy host: `127.0.0.1:3307`

Kiểm tra trạng thái:

```powershell
docker compose ps
docker compose logs --tail=100 app
```

Database được tạo và import tự động ở lần chạy đầu tiên. Dữ liệu MySQL mặc định được lưu tại `../godashop-db`, bên ngoài repository.

## Cấu hình môi trường

Sao chép `.env.example` thành `.env`, sau đó cập nhật các giá trị phù hợp:

```env
APP_URL=http://localhost

SMTP_USERNAME=your_email@example.com
SMTP_SECRET=your_gmail_app_password
SHOP_OWNER=your_email@example.com

STRIPE_SECRET_KEY=sk_test_your_key
STRIPE_WEBHOOK_SECRET=whsec_your_secret

JWT_KEY=replace_with_a_long_random_value
APP_DEBUG=0
```

Không commit `.env`, khóa Stripe, mật khẩu email hoặc `config.local.php`. Các tệp này đã được khai báo trong `.gitignore`.

Sau mỗi lần thay đổi `.env`, tạo lại container ứng dụng:

```powershell
docker compose up -d --force-recreate app
```

## Stripe sandbox

1. Bật Sandbox/Test mode trong Stripe Dashboard.
2. Lấy Secret key bắt đầu bằng `sk_test_` tại **Developers → API keys** và đặt vào `STRIPE_SECRET_KEY`.
3. Tại **Workbench → Webhooks**, tạo event destination trỏ đến:

```text
https://your-domain.example/index.php?c=payment&a=stripeWebhook
```

4. Đăng ký các event:

```text
checkout.session.completed
checkout.session.async_payment_succeeded
checkout.session.async_payment_failed
checkout.session.expired
```

5. Sao chép Signing secret bắt đầu bằng `whsec_` vào `STRIPE_WEBHOOK_SECRET`.
6. Tạo lại container ứng dụng.

Thẻ thử nghiệm thanh toán thành công:

```text
Số thẻ: 4242 4242 4242 4242
Ngày hết hạn: một ngày trong tương lai, ví dụ 12/34
CVC: 123
```

Không sử dụng thẻ thật trong sandbox. Với localhost hoặc máy chủ chưa có HTTPS, dùng Stripe CLI để chuyển tiếp webhook:

```powershell
stripe login
stripe listen --forward-to "http://localhost/index.php?c=payment&a=stripeWebhook"
```

## Domain và triển khai

Đặt URL thực tế trong `.env`, không thêm dấu `/` ở cuối:

```env
APP_URL=https://your-domain.example
```

Sau đó chạy:

```powershell
docker compose up -d --build --force-recreate
```

Khi triển khai thật, cần bật HTTPS, thay toàn bộ mật khẩu mặc định, dùng Stripe live key và tạo webhook riêng ở Live mode.

## Lệnh thường dùng

```powershell
# Khởi động
docker compose up -d

# Build lại ứng dụng sau khi sửa Dockerfile hoặc mã nguồn
docker compose up -d --build app

# Xem log
docker compose logs -f app

# Dừng dịch vụ
docker compose down

# Kiểm tra cú pháp một tệp PHP
docker compose exec app php -l site/controller/PaymentController.php
```

Không xóa thư mục dữ liệu MySQL nếu chưa sao lưu. `docker compose down` không xóa dữ liệu; việc xóa thủ công `../godashop-db` sẽ làm mất database hiện tại.

## Cấu trúc chính

```text
admin/        Trang quản trị
site/         Giao diện và controller phía khách hàng
model/        Entity và repository
service/      Các dịch vụ, gồm Stripe
migrations/   Database gốc và migration
upload/       Hình ảnh sản phẩm
docker/       Cấu hình Apache/PHP cho container
tools/        Script kiểm thử và hỗ trợ phát triển
```

Hướng dẫn chi tiết hơn có trong [SETUP.md](SETUP.md). Các lưu ý rà soát kỹ thuật nằm trong [AUDIT.md](AUDIT.md).


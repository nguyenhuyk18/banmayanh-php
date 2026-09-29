# GodaShop trên XAMPP

## Chạy local

1. Bật Apache và MariaDB trong XAMPP.
2. Dùng database `godashop_recovered_20260919` (được chọn trong `config.php`). Nếu tạo database khác, sửa hằng `DBNAME` và import bản SQL của shop trước khi chạy migration.
3. Áp dụng lần lượt `migrations/20260920_articles_stripe.sql` và `migrations/20260927_camera_catalog.sql` cho database đang dùng. Cả hai migration có thể chạy lại an toàn; migration thứ hai chuyển dữ liệu demo sang catalog máy ảnh.
4. Mở `http://localhost/backend/godashop/` và `http://localhost/backend/godashop/admin/login.php`.
5. Chạy `python tools/smoke.py` để kiểm tra các chức năng local. Script tự xóa bản ghi thử.

Nếu chạy bằng Docker sau khi cập nhật mã, rebuild image để lấy Dockerfile và PHP config mới: `docker compose build --no-cache app`, sau đó `docker compose up -d`. Truy cập `http://localhost/`. `docker compose up -d` chỉ khởi động image cũ và không tự build lại khi Dockerfile thay đổi.

Docker Compose tự chạy MySQL trong service `db`; app dùng hostname `db`, không dùng XAMPP hoặc `localhost`. Database mới được import từ `migrations/godashop_recovered_20260919.sql` vào thư mục `../godashop-db`, nằm ngoài source và ngoài Docker-managed volume. Nếu muốn khởi tạo lại database mới, dừng stack rồi xóa riêng thư mục dữ liệu đó sau khi đã sao lưu.

Với database Docker đã tồn tại từ trước, áp dụng catalog máy ảnh bằng lệnh `docker compose exec -T db sh -c "mysql -uroot -proot godashop_recovered_20260919 < /docker-entrypoint-initdb.d/003-camera-catalog.sql"`. Database tạo mới sẽ tự chạy migration này.

phpMyAdmin lưu session ra `docker-data/phpmyadmin:/sessions`, nên session không nằm trong writable layer của container.

Để máy khác cùng mạng truy cập, mở Windows PowerShell bằng quyền Administrator và chạy `New-NetFirewallRule -DisplayName "GodaShop Docker HTTP" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow`. Sau đó truy cập `http://192.168.1.111/`; port 80 là mặc định nên không cần ghi `:80`. Máy khách phải cùng mạng LAN và địa chỉ IP phải còn đúng.

Kiểm tra nhanh trên máy Docker: `Test-NetConnection 127.0.0.1 -Port 80` phải có `TcpTestSucceeded: True`. Từ máy khác trong LAN chạy `Test-NetConnection 192.168.1.111 -Port 80`; nếu máy chủ đúng nhưng máy khác sai thì mở firewall và chuyển network profile Windows sang Private. Nếu cả máy chủ sai, kiểm tra `docker compose ps` và port 80 có bị Apache/XAMPP chiếm bằng `netstat -ano | findstr :80`.

Nếu muốn Docker app dùng MySQL XAMPP trên máy host, tạo file `.env` từ `.env.example` rồi đặt `DB_HOST=host.docker.internal`, `APP_DB_PASSWORD=` và `DB_PASSWORD=`. Database XAMPP phải đang chạy và cho phép kết nối TCP; dữ liệu XAMPP không bị Docker xóa hay sửa.

## Stripe test mode

1. Tạo tài khoản Stripe ở quốc gia được Stripe hỗ trợ và lấy **secret key test mode** trong Stripe Dashboard. Sao chép `config.local.php.example` thành `config.local.php`; thay hai giá trị mẫu bằng khóa test của tài khoản. File này bị Apache chặn truy cập qua HTTP và không nên đưa vào Git hay chia sẻ công khai.
2. Khởi động lại Apache, mở thanh toán và chọn **Stripe (thẻ)**. Checkout dùng VND, một loại tiền không có đơn vị thập phân trong Stripe.
3. Để Stripe gửi webhook tới XAMPP, dùng Stripe CLI: `stripe.cmd listen --forward-to "http://localhost/backend/godashop/index.php?c=payment&a=stripeWebhook"` (Windows) hoặc `stripe listen --forward-to "http://localhost/backend/godashop/index.php?c=payment&a=stripeWebhook"` (macOS/Linux). Chép `whsec_...` do CLI in ra vào `config.local.php`, rồi khởi động lại Apache. Giữ cửa sổ listener chạy khi thử thanh toán.
4. Đăng nhập tài khoản khách hàng trên web, thêm sản phẩm vào giỏ, chọn Stripe và hoàn tất Checkout test bằng thẻ `4242 4242 4242 4242`, hạn sử dụng trong tương lai như `12/34`, CVC bất kỳ gồm 3 chữ số. Sau khi quay về web, đơn hàng phải hiển thị **Đã thanh toán**. Đây là [thẻ test chính thức của Stripe](https://docs.stripe.com/testing); không dùng thẻ thật.
5. Webhook xử lý `checkout.session.completed`, `checkout.session.async_payment_succeeded`, `checkout.session.expired` và `checkout.session.async_payment_failed`. Trang thành công cũng kiểm tra phiên thanh toán trực tiếp với API Stripe để hỗ trợ XAMPP khi webhook chưa được chuyển tiếp.

Không thể hoàn tất thanh toán Stripe khi chưa có khóa test hợp lệ. Nếu `STRIPE_SECRET_KEY` chưa được cấu hình, lựa chọn Stripe bị vô hiệu hóa; COD vẫn hoạt động. Tài khoản Stripe có thể từ chối tổng tiền VND quá nhỏ do mức tối thiểu quy đổi theo tỷ giá.

## Khi đưa lên internet

Trước khi mở cho người dùng thật: thiết lập HTTPS, đổi khóa/tài khoản mặc định trong `config.php`, dùng khóa Stripe live của tài khoản hợp lệ, đăng ký URL webhook công khai và kiểm tra webhook trên máy chủ triển khai. Cập nhật `DBNAME` và thông tin kết nối theo host thực tế. Không đưa thư mục `tools`, file SQL, khóa Stripe hoặc bản sao database lên vùng web công khai. XAMPP localhost chưa đáp ứng yêu cầu chạy công khai trên internet.

<?php
// __DIR__ là thư mục chứa file chạy, cụ thể là C:/.../godashop/
define('ABSPATH', __DIR__ . '/'); 
// C:/.../godashop/site
define('ABSPATH_SITE', ABSPATH . 'site/'); 

define('SERVERNAME', getenv('DB_HOST') ?: 'localhost');
define('USERNAME', getenv('DB_USERNAME') ?: 'root');
define('PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DBNAME', getenv('DB_NAME') ?: 'godashop_recovered_20260919');

// SMTP (send mail)
// from
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'kewwihuy@gmail.com');
define('SMTP_SECRET', getenv('SMTP_SECRET') ?: 'change_me');
// server mail
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');

// to (chủ cửa hàng)
define('SHOP_OWNER', getenv('SHOP_OWNER') ?: 'kewwihuy@gmail.com');
define('SHOP_ADDRESS', getenv('SHOP_ADDRESS') ?: 'Thành phố Hồ Chí Minh');
define('SCHOOL_MAP_QUERY', getenv('SCHOOL_MAP_QUERY') ?: SHOP_ADDRESS);

define('GOOGLE_RECAPTCHA_SITE', getenv('GOOGLE_RECAPTCHA_SITE') ?: 'replace_with_public_site_key');
define('GOOGLE_RECAPTCHA_SECRET', getenv('GOOGLE_RECAPTCHA_SECRET') ?: 'replace_with_secret_key');

define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: 'replace_with_google_client_id');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: 'replace_with_google_client_secret');

define('JWT_KEY', getenv('JWT_KEY') ?: 'change_this_jwt_key');

// Private local keys (for example Stripe test credentials) are kept outside this file.
if (is_file(__DIR__ . '/config.local.php')) require __DIR__ . '/config.local.php';


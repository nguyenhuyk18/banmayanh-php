<?php
// Start buffering before config/vendor files are loaded. This keeps accidental BOM/whitespace
// in a mounted Docker file from sending output before redirects and JSON headers.
ob_start();
session_start();

require 'vendor/autoload.php';
$router = new AltoRouter();

use Cocur\Slugify\Slugify;
$slugify = new Slugify();

// import config & connectDB
require 'config.php';
require ABSPATH . 'connectDB.php';

// require cái load vô
require ABSPATH_SITE . 'load.php';

// import models
require ABSPATH . 'bootstrap.php';
$router->setBasePath(rtrim(get_base_path(), '/'));
ob_start('protect_html');

// map homepage
$router->map( 'GET', '/', function() {
    // require './site/controller/HomeController.php';
    $controller = new HomeController();
    $controller->index();
	// echo 'Trang Chủ';
}, 'home' );

$router->map('GET', '/gioi-thieu.html', function() { (new ArticleController())->about(); }, 'about');
$router->map('GET', '/bai-viet.html', function() { (new ArticleController())->index(); }, 'articles');
$router->map('GET', '/bai-viet/[*:slug]-[i:id].html', function($slug, $id) { (new ArticleController())->detail($slug, $id); }, 'articleDetail');
$router->map('GET', '/gio-hang.html', function() { (new CartController())->page(); }, 'cartPage');

// trang sản phẩm
$router->map( 'GET', '/san-pham.html', function() {
    $controller = new ProductController();
    $controller->index();
	// echo 'Trang Chủ';
}, 'product' );

// Chính sách đổi trả
$router->map( 'GET', '/chinh-sach-doi-tra.html', function() {
    $controller = new PolicyController();
    $controller->return();
	// echo 'Trang Chủ';
}, 'return'  );

// chính sách thanh toán
$router->map( 'GET', '/chinh-sach-thanh-toan.html', function() {
    $controller = new PolicyController();
    $controller->payment();
	// echo 'Trang Chủ';
}, 'payment' );

// chính sách giao hàng
$router->map( 'GET', '/chinh-sach-giao-hang.html', function() {
    $controller = new PolicyController();
    $controller->delivery();
	// echo 'Trang Chủ';
}, 'delivery');

// liên hệ
$router->map( 'GET', '/lien-he.html', function() {
    $controller = new ContactController();
    $controller->form();
	// echo 'Trang Chủ';
}, 'contact');

//  trang danh mục
// danh-muc/kem-chong-nang-1.html
// slug là kem-chong-nang
// id là 1
$router->map( 'GET', '/danh-muc/[*:slug]-[i:id].html', function($slug, $id) {
    $controller = new ProductController();
    $controller->index($id);
	// echo 'Trang Chủ';
}, 'category');



// san-pham/phan-phu-carslan-dang-nen-vo-xam-mau-trong-suot-8g-122580.html
$router->map( 'GET', '/san-pham/[*:slug]-[i:id].html', function($slug ,$id ) {
    $controller = new ProductController();
    $controller->detail($id);
}, 'productDetail');


// map user details page
$router->map( 'GET', '/user/[i:id]', function( $id ) {
	echo $id;
});

// match current request url
$match = isset($_GET['c']) ? false : $router->match();
$routeName = is_array($match) ? $match['name'] : '';
// var_dump($match);

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] );
} else {
    // router
    // lấy c & a
    $c = input_text('c', $_GET, 'home');
    $a = input_text('a', $_GET, 'index');



    // Tạo đối tượng controller và gọi hàm tương úng chạy
    if (!isset($_GET['c']) && !in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), [app_url(), app_url('index.php')], true)) {
        http_response_code(404);
        exit('Không tìm thấy trang.');
    }
    dispatch_action($c, $a, [
        'home' => ['index'], 'article' => ['about','index','detail'], 'product' => ['index','detail','storeComment'],
        'auth' => ['login','logout'],
        'customer' => ['show','updateAccount','shippingDefault','updateShippingDefault','orders','orderDetail','notExistingEmail','register','activeAccount','forgotPassword','resetPasword','updatePassword'],
        'cart' => ['index','page','add','update','delete'], 'payment' => ['checkout','order','complete','stripeReturn','stripeCancel','stripeResume','stripeWebhook'],
        'address' => ['getDistricts','getWards','getShippingFee'],
        'contact' => ['form','sendEmail','subscribe'], 'policy' => ['return','payment','delivery']
    ]);
}

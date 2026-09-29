<?php
class CartController {
    private function respond($cart, $message = 'Đã cập nhật giỏ hàng.') {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
            json_response($cart);
            return;
        }
        $_SESSION['success'] = $message;
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $path = parse_url($referer, PHP_URL_PATH) ?: '';
        $host = parse_url($referer, PHP_URL_HOST);
        if ($host === $_SERVER['SERVER_NAME'] && strpos($path, get_base_path() . '/') === 0) {
            $destination = $path;
            $query = parse_url($referer, PHP_URL_QUERY);
            if ($query) $destination .= '?' . $query;
        } else {
            $destination = app_url();
        }
        header('Location: ' . $destination, true, 303);
        exit;
    }
    function index() { json_response((new CartStorage())->fetch()); }
    function page() {
        $cart = (new CartStorage())->fetch();
        require ABSPATH_SITE . 'view/cart/index.php';
    }
    function add() {
        $storage = new CartStorage();
        $cart = $storage->fetch();
        $cart->addProduct(positive_id($_POST['product_id'] ?? 0), positive_id($_POST['qty'] ?? 0));
        $storage->store($cart);
        $this->respond($cart, 'Đã thêm sản phẩm vào giỏ hàng.');
    }
    function delete() {
        $storage = new CartStorage();
        $cart = $storage->fetch();
        $cart->deleteProduct(positive_id($_POST['product_id'] ?? 0));
        $storage->store($cart);
        $this->respond($cart);
    }
    function update() {
        $storage = new CartStorage();
        $cart = $storage->fetch();
        $id = positive_id($_POST['product_id'] ?? 0);
        $qty = positive_id($_POST['qty'] ?? 0);
        $cart->deleteProduct($id);
        $cart->addProduct($id, $qty);
        $storage->store($cart);
        $this->respond($cart);
    }
}

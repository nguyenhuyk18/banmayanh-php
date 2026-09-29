<?php
class CartStorage {
    function store($cart) {
        // Prices and names are always loaded from the database.
        $_SESSION['cart_items'] = array_map(function ($item) { return $item['qty']; }, $cart->items);
    }
    function fetch() {
        $cart = new Cart();
        $quantities = $_SESSION['cart_items'] ?? null;
        if (!is_array($quantities)) {
            $quantities = [];
            $legacy = json_decode($_COOKIE['cart'] ?? '', true);
            if (is_array($legacy) && is_array($legacy['items'] ?? null)) {
                foreach ($legacy['items'] as $item) {
                    if (is_array($item) && isset($item['product_id'], $item['qty']) && is_scalar($item['product_id'])) {
                        $quantities[$item['product_id']] = $item['qty'];
                    }
                }
            }
        }
        foreach ($quantities as $id => $qty) {
            try { $cart->addProduct(positive_id($id), positive_id($qty)); }
            catch (InvalidArgumentException | DomainException $error) { /* Unavailable product. */ }
        }
        $this->store($cart);
        return $cart;
    }
    function clear() {
        $_SESSION['cart_items'] = [];
        unset($_SESSION['cart']);
        setcookie('cart', '', time() - 3600, app_url());
        setcookie('cart', '', time() - 3600, '/');
    }
}

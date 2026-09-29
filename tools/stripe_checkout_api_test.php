<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = 80;
$_SERVER['DOCUMENT_ROOT'] = 'C:/xampp/htdocs';
require __DIR__ . '/../config.php';
require __DIR__ . '/../bootstrap.php';
try {
    $session = StripeService::checkout(999999999, [[
        'name'=>'GodaShop checkout API test', 'unit_price'=>20000, 'qty'=>1
    ]], 0, 'codex-stripe-test@example.invalid');
    if (empty($session['id']) || empty($session['url'])) throw new RuntimeException('Stripe did not return a Checkout URL');
    $expired = StripeService::request('POST', 'checkout/sessions/' . rawurlencode($session['id']) . '/expire');
    if (($expired['status'] ?? '') !== 'expired') throw new RuntimeException('Stripe session did not expire');
    echo "Stripe test Checkout session created and expired successfully\n";
} catch (Throwable $error) {
    echo get_class($error) . ': ' . $error->getMessage() . PHP_EOL;
    exit(1);
}

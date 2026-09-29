<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../service/StripeService.php';
try {
    echo 'Configured: ' . (StripeService::configured() ? 'yes' : 'no') . '; key length: ' . strlen(StripeService::secretKey()) . PHP_EOL;
    $account = StripeService::request('GET', 'account');
    echo 'Stripe API test key accepted; account country: ' . ($account['country'] ?? 'unknown') . PHP_EOL;
} catch (Throwable $error) {
    echo get_class($error) . ': ' . $error->getMessage() . PHP_EOL;
    exit(1);
}

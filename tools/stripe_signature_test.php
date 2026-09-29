<?php
require __DIR__ . '/../service/StripeService.php';

$event = json_encode(['id'=>'evt_test_123','type'=>'checkout.session.completed','data'=>['object'=>['id'=>'cs_test_123']]]);
$secret = 'whsec_local_test';
$time = time();
$signature = 't=' . $time . ',v1=' . hash_hmac('sha256', $time . '.' . $event, $secret);
if (StripeService::verifyWebhook($event, $signature, $secret, $time)['id'] !== 'evt_test_123') exit(1);
try { StripeService::verifyWebhook($event . ' ', $signature, $secret, $time); exit(2); }
catch (InvalidArgumentException $expected) {}
try { StripeService::verifyWebhook($event, $signature, $secret, $time + 301); exit(3); }
catch (InvalidArgumentException $expected) {}
echo "Stripe webhook signature checks passed\n";

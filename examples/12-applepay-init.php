<?php

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Responses\ApplePayInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payload is the base64-encoded payment token from the Apple Pay JS API
// (ApplePaySession → completeMerchantValidation → ApplePayPayment.token)
$applePayPayload = 'REPLACE_WITH_BASE64_APPLE_PAY_TOKEN';

$request = new ApplePayInitRequest();
$request->setMerchant($merchant);
$request->setOrderNumber('ORD-'.time());
$request->setTotalAmount(100.00);
$request->setCurrency(Currency::HUF);
$request->setPayload($applePayPayload);
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);

$response = $client->send($request);

assert($response instanceof ApplePayInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

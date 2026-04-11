<?php

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Responses\GooglePayInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payload is the JSON payment token from the Google Pay API
// (PaymentData.paymentMethodData.tokenizationData.token)
$googlePayPayload = 'REPLACE_WITH_GOOGLEPAY_TOKEN_JSON';

$request = new GooglePayInitRequest();
$request->setMerchant($merchant);
$request->setOrderNumber('ORD-'.time());
$request->setTotalAmount(100.00);
$request->setCurrency(Currency::HUF);
$request->setPayload($googlePayPayload);
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);

$response = $client->send($request);

assert($response instanceof GooglePayInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

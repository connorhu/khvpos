<?php

use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Responses\OneClickInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// origPayId is the payId from a previous successful card payment (02-payment-init.php flow)
$originalPayId = 'REPLACE_WITH_PAYID_OF_PREVIOUS_CARD_PAYMENT';

$request = new OneClickInitRequest();
$request->setMerchant($merchant);
$request->setOriginalPaymentId($originalPayId);
$request->setOrderNumber('ORD-'.time());
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);

$response = $client->send($request);

assert($response instanceof OneClickInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

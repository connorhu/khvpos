<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\Responses\PaymentReverseResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// Reverses a payment in pending/processing state (paymentStatus 1 or 2).
// payId is returned by 02-payment-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_02';

$request = new PaymentReverseRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);

$response = $client->send($request);

assert($response instanceof PaymentReverseResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId());
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

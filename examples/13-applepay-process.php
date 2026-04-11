<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use KHTools\VPos\Responses\ApplePayProcessResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payId is returned by 12-applepay-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_12';

$request = new ApplePayProcessRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);

$response = $client->send($request);

assert($response instanceof ApplePayProcessResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

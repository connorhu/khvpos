<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\OneClickProcessRequest;
use KHTools\VPos\Responses\OneClickProcessResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payId is returned by 09-oneclick-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_09';

$request = new OneClickProcessRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);

$response = $client->send($request);

assert($response instanceof OneClickProcessResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

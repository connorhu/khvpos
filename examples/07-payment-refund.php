<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\Responses\PaymentRefundResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// Refunds a captured payment (paymentStatus 4).
// payId is returned by 02-payment-init.php after capture
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_02';

$request = new PaymentRefundRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);
// Optional: pass an amount for partial refund; omit for full refund
// $request->setAmount(50.00); // refund 50.00 HUF

$response = $client->send($request);

assert($response instanceof PaymentRefundResponse);

echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

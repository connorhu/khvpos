<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Responses\PaymentCloseResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// Closes an authorized payment (paymentStatus 3 or 5).
// payId is returned by 02-payment-init.php after the customer completes the card form
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_02';

$request = new PaymentCloseRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);
// Optional: pass a lower amount in whole HUF for partial capture
// $request->setTotalAmount(50); // closes 50.00 HUF instead of the full amount

$response = $client->send($request);

assert($response instanceof PaymentCloseResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId());
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

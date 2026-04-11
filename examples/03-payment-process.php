<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payId is returned by 02-payment-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_02';

$initResponse = new PaymentInitResponse();
$initResponse->setPaymentId($payId);

// PaymentProcessRequest does not use send() — it generates a browser redirect URL
$request = PaymentProcessRequest::initWith($initResponse, $merchant);

$url = $client->getPaymentUrlWithPaymentProcessRequest($request);

echo sprintf('Redirect the customer to: %s'."\n", $url);

<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Responses\ApplePayEchoResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$request = new ApplePayEchoRequest();
$request->setMerchant((new Merchant())->setMerchantId($_ENV['MERCHANT_ID']));

$response = $client->send($request);

assert($response instanceof ApplePayEchoResponse);

echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

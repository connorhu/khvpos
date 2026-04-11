<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\GooglePayEchoRequest;
use KHTools\VPos\Responses\GooglePayEchoResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$request = new GooglePayEchoRequest();
$request->setMerchant((new Merchant())->setMerchantId($_ENV['MERCHANT_ID']));

$response = $client->send($request);

assert($response instanceof GooglePayEchoResponse);

echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

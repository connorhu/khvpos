<?php

use KHTools\VPos\Models\CartItem;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

$cartItem = new CartItem();
$cartItem->setName('Sample product');
$cartItem->setQuantity(1);
$cartItem->setAmount(100.00);

$request = new PaymentInitRequest();
$request->setMerchant($merchant);
$request->setOrderNumber('ORD-'.time());
$request->setTotalAmount(100.00);
$request->setCurrency(Currency::HUF);
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);
$request->addCartItem($cartItem);

$response = $client->send($request);

assert($response instanceof PaymentInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId());
echo sprintf('paymentStatus: %d'."\n", $response->getPaymentStatus());
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());

# Examples & README Docs Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add one runnable example PHP file per supported API request (15 new files) and extend `README.md` with Installation, Symfony Bundle, and Payum integration documentation.

**Architecture:** Each example file is self-contained, requires `bootstrap.php` for the `VPosClient`, uses real request/response classes, and prints the key response fields. Where a request depends on data from a previous step (e.g. `payId`), a placeholder variable with an explanatory comment is used. README updates add three new sections directly after the support chart.

**Tech Stack:** PHP 8.2+, `KHTools\VPos` library classes, no new dependencies.

---

## File Structure

**Create:**
- `examples/02-payment-init.php`
- `examples/03-payment-process.php`
- `examples/04-payment-status.php`
- `examples/05-payment-reverse.php`
- `examples/06-payment-close.php`
- `examples/07-payment-refund.php`
- `examples/08-oneclick-echo.php`
- `examples/09-oneclick-init.php`
- `examples/10-oneclick-process.php`
- `examples/11-applepay-echo.php`
- `examples/12-applepay-init.php`
- `examples/13-applepay-process.php`
- `examples/14-googlepay-echo.php`
- `examples/15-googlepay-init.php`
- `examples/16-googlepay-process.php`

**Modify:**
- `README.md` — add three sections after support chart

---

## Reference: Key API facts

Before implementing, understand these:

- `Merchant::setMerchantId()` returns `static` → chainable: `(new Merchant())->setMerchantId($_ENV['MERCHANT_ID'])`
- `Currency` and `HttpMethod` are PHP enums. Use cases directly: `Currency::HUF`, `HttpMethod::Post`. **Do not use `::from()`** — they are not backed enums.
- `PaymentProcessRequest` is the exception: it does **not** use `$client->send()`. Use `$client->getPaymentUrlWithPaymentProcessRequest($request)` which returns a string URL.
- `PaymentCloseRequest::setTotalAmount()` takes `?int` in whole currency units (e.g. `50` = 50 HUF), **not** fillér/cents. The setter multiplies internally by 100. This differs from other request classes which take `float`.
- `PaymentRefundRequest::setAmount(?float)` — passing `null` triggers full refund.
- All response classes share `getResultCode(): ?int` and `getResultMessage(): ?string` via `CommonResponseTrait`.
- `PaymentInitResponse::getPaymentId()` returns the `payId` string (serialized as `payId` in JSON).

---

### Task 1: Core payment init + process examples

**Files:**
- Create: `examples/02-payment-init.php`
- Create: `examples/03-payment-process.php`

- [ ] **Step 1: Create `examples/02-payment-init.php`**

```php
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
```

- [ ] **Step 2: Create `examples/03-payment-process.php`**

```php
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
```

- [ ] **Step 3: Verify syntax**

```bash
php -l examples/02-payment-init.php && php -l examples/03-payment-process.php
```

Expected: `No syntax errors detected` for both files.

- [ ] **Step 4: Commit**

```bash
git add examples/02-payment-init.php examples/03-payment-process.php
git commit -m "docs: add payment init and process examples"
```

---

### Task 2: Payment status, reverse, close, refund examples

**Files:**
- Create: `examples/04-payment-status.php`
- Create: `examples/05-payment-reverse.php`
- Create: `examples/06-payment-close.php`
- Create: `examples/07-payment-refund.php`

- [ ] **Step 1: Create `examples/04-payment-status.php`**

```php
<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payId is returned by 02-payment-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_02';

$request = new PaymentStatusRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);

$response = $client->send($request);

assert($response instanceof PaymentStatusResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId());
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());
```

- [ ] **Step 2: Create `examples/05-payment-reverse.php`**

```php
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
```

- [ ] **Step 3: Create `examples/06-payment-close.php`**

```php
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
```

Note: `PaymentCloseRequest::setTotalAmount()` takes `?int` in whole currency units (e.g. `50` = 50 HUF), not fillér. This differs from `PaymentInitRequest::setTotalAmount(float)` which takes HUF as a float.

- [ ] **Step 4: Create `examples/07-payment-refund.php`**

```php
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
```

- [ ] **Step 5: Verify syntax**

```bash
php -l examples/04-payment-status.php && \
php -l examples/05-payment-reverse.php && \
php -l examples/06-payment-close.php && \
php -l examples/07-payment-refund.php
```

Expected: `No syntax errors detected` for all four files.

- [ ] **Step 6: Commit**

```bash
git add examples/04-payment-status.php examples/05-payment-reverse.php examples/06-payment-close.php examples/07-payment-refund.php
git commit -m "docs: add payment status, reverse, close, and refund examples"
```

---

### Task 3: OneClick examples

**Files:**
- Create: `examples/08-oneclick-echo.php`
- Create: `examples/09-oneclick-init.php`
- Create: `examples/10-oneclick-process.php`

- [ ] **Step 1: Create `examples/08-oneclick-echo.php`**

```php
<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Responses\OneClickEchoResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$request = new OneClickEchoRequest();
$request->setMerchant((new Merchant())->setMerchantId($_ENV['MERCHANT_ID']));

$response = $client->send($request);

assert($response instanceof OneClickEchoResponse);

echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());
```

- [ ] **Step 2: Create `examples/09-oneclick-init.php`**

```php
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
```

- [ ] **Step 3: Create `examples/10-oneclick-process.php`**

```php
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
```

- [ ] **Step 4: Verify syntax**

```bash
php -l examples/08-oneclick-echo.php && \
php -l examples/09-oneclick-init.php && \
php -l examples/10-oneclick-process.php
```

Expected: `No syntax errors detected` for all three files.

- [ ] **Step 5: Commit**

```bash
git add examples/08-oneclick-echo.php examples/09-oneclick-init.php examples/10-oneclick-process.php
git commit -m "docs: add oneclick echo, init, and process examples"
```

---

### Task 4: Apple Pay examples

**Files:**
- Create: `examples/11-applepay-echo.php`
- Create: `examples/12-applepay-init.php`
- Create: `examples/13-applepay-process.php`

- [ ] **Step 1: Create `examples/11-applepay-echo.php`**

```php
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
```

- [ ] **Step 2: Create `examples/12-applepay-init.php`**

```php
<?php

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Responses\ApplePayInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payload is the base64-encoded payment token from the Apple Pay JS API
// (ApplePaySession → completeMerchantValidation → ApplePayPayment.token)
$applePayPayload = 'REPLACE_WITH_BASE64_APPLE_PAY_TOKEN';

$request = new ApplePayInitRequest();
$request->setMerchant($merchant);
$request->setOrderNumber('ORD-'.time());
$request->setTotalAmount(100.00);
$request->setCurrency(Currency::HUF);
$request->setPayload($applePayPayload);
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);

$response = $client->send($request);

assert($response instanceof ApplePayInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());
```

- [ ] **Step 3: Create `examples/13-applepay-process.php`**

```php
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
```

- [ ] **Step 4: Verify syntax**

```bash
php -l examples/11-applepay-echo.php && \
php -l examples/12-applepay-init.php && \
php -l examples/13-applepay-process.php
```

Expected: `No syntax errors detected` for all three files.

- [ ] **Step 5: Commit**

```bash
git add examples/11-applepay-echo.php examples/12-applepay-init.php examples/13-applepay-process.php
git commit -m "docs: add Apple Pay echo, init, and process examples"
```

---

### Task 5: Google Pay examples

**Files:**
- Create: `examples/14-googlepay-echo.php`
- Create: `examples/15-googlepay-init.php`
- Create: `examples/16-googlepay-process.php`

- [ ] **Step 1: Create `examples/14-googlepay-echo.php`**

```php
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
```

- [ ] **Step 2: Create `examples/15-googlepay-init.php`**

```php
<?php

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Responses\GooglePayInitResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payload is the JSON payment token from the Google Pay API
// (PaymentData.paymentMethodData.tokenizationData.token)
$googlePayPayload = 'REPLACE_WITH_GOOGLEPAY_TOKEN_JSON';

$request = new GooglePayInitRequest();
$request->setMerchant($merchant);
$request->setOrderNumber('ORD-'.time());
$request->setTotalAmount(100.00);
$request->setCurrency(Currency::HUF);
$request->setPayload($googlePayPayload);
$request->setReturnUrl('https://example.com/payment/return');
$request->setReturnMethod(HttpMethod::Post);

$response = $client->send($request);

assert($response instanceof GooglePayInitResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());
```

- [ ] **Step 3: Create `examples/16-googlepay-process.php`**

```php
<?php

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\GooglePayProcessRequest;
use KHTools\VPos\Responses\GooglePayProcessResponse;
use KHTools\VPos\VPosClient;

$client = require_once __DIR__.'/bootstrap.php';

assert($client instanceof VPosClient);

$merchant = (new Merchant())->setMerchantId($_ENV['MERCHANT_ID']);

// payId is returned by 15-googlepay-init.php
$payId = 'REPLACE_WITH_PAYID_FROM_STEP_15';

$request = new GooglePayProcessRequest();
$request->setMerchant($merchant);
$request->setPaymentId($payId);

$response = $client->send($request);

assert($response instanceof GooglePayProcessResponse);

echo sprintf('payId: %s'."\n", $response->getPaymentId() ?? 'null');
echo sprintf('paymentStatus: %s'."\n", $response->getPaymentStatus() ?? 'null');
echo sprintf('resultCode: %d'."\n", $response->getResultCode());
echo sprintf('resultMessage: %s'."\n", $response->getResultMessage());
```

- [ ] **Step 4: Verify syntax**

```bash
php -l examples/14-googlepay-echo.php && \
php -l examples/15-googlepay-init.php && \
php -l examples/16-googlepay-process.php
```

Expected: `No syntax errors detected` for all three files.

- [ ] **Step 5: Commit**

```bash
git add examples/14-googlepay-echo.php examples/15-googlepay-init.php examples/16-googlepay-process.php
git commit -m "docs: add Google Pay echo, init, and process examples"
```

---

### Task 6: README — Installation, Symfony Bundle, Payum Integration

**Files:**
- Modify: `README.md`

The current README ends its main content with the support chart and then link definitions. Add the three new sections between the support chart and the link definitions block.

The link definitions block starts with `  [test status image]:` — insert new content just before that line.

- [ ] **Step 1: Read the current README**

Run: `cat README.md`

Verify: the file ends with a support chart table, then a blank line, then link definitions starting with `  [test status image]:`.

- [ ] **Step 2: Add the three new sections to `README.md`**

Replace the line:

```
  [test status image]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml/badge.svg?branch=master
```

with:

```markdown
## Installation

```bash
composer require connorhu/khvpos
```

---

## Symfony Bundle

There is no Flex recipe — bundle registration is manual.

### Register the bundle

```php
// config/bundles.php
return [
    KHTools\VPos\Bundle\KHVPosBundle::class => ['all' => true],
];
```

### Configuration

```yaml
# config/packages/khvpos.yaml
khvpos:
    test: true   # set to false for production
    merchants:
        default:
            merchant_id: '%env(KHVPOS_MERCHANT_ID)%'
            private_key_path: '%kernel.project_dir%/config/keys/merchant.pem'
            private_key_passphrase: '%env(KHVPOS_PRIVATE_KEY_PASSPHRASE)%'  # optional, default ''
            currency: HUF
```

A single `VPosClient` service is registered as `khvpos.vpos_client` (also autowirable as `VPosClient`). Inject `MerchantProviderInterface` to look up the correct merchant by currency:

```php
use KHTools\VPos\Bundle\Providers\MerchantProviderInterface;
use KHTools\VPos\VPosClient;

public function __construct(
    private VPosClient $client,
    private MerchantProviderInterface $merchantProvider,
) {}

public function pay(): void
{
    $merchant = $this->merchantProvider->getMerchant('HUF');
    $request = new PaymentInitRequest();
    $request->setMerchant($merchant);
    // ...
}
```

### Multiple merchants (multiple currencies)

```yaml
khvpos:
    test: false
    merchants:
        huf:
            merchant_id: '%env(KHVPOS_HUF_MERCHANT_ID)%'
            private_key_path: '%kernel.project_dir%/config/keys/huf.pem'
            currency: HUF
        eur:
            merchant_id: '%env(KHVPOS_EUR_MERCHANT_ID)%'
            private_key_path: '%kernel.project_dir%/config/keys/eur.pem'
            currency: EUR
```

`$merchantProvider->getMerchant('EUR')` returns the EUR merchant.

### All configuration options

| Key | Type | Default | Description |
|---|---|---|---|
| `test` | bool | `true` | Use sandbox endpoint |
| `mips_public_key_path` | string | `null` | Path to MIPS public key PEM; `null` uses the bundled key |
| `version` | string | `rv1` | API version |
| `merchants.<name>.merchant_id` | string | required | K&H merchant ID |
| `merchants.<name>.private_key_path` | string | required | Path to RSA private key PEM |
| `merchants.<name>.private_key_passphrase` | string | `''` | Optional key passphrase |
| `merchants.<name>.currency` | string | `HUF` | Default currency (`HUF`, `EUR`, `USD`) |

---

## Payum Integration

Requires `payum/core ^1.7`. For Symfony, also install `payum/payum-bundle`:

```bash
composer require payum/payum-bundle
```

With `payum/payum-bundle` installed and a `khvpos:` config block present, the `khvpos` Payum gateway is registered automatically — no extra YAML required:

```php
$gateway = $payum->getGateway('khvpos');
$gateway->execute(new Capture($payment));
```

The first configured merchant is used. Supported Payum requests: `Capture`, `Authorize`, `Refund`, `Cancel`, `Sync`, `GetHumanStatus`, `Convert`.

---

  [test status image]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml/badge.svg?branch=master
```

(The `---` before `[test status image]` separates the new content from the link definitions visually; the link definitions themselves are unchanged.)

- [ ] **Step 3: Verify README renders correctly**

```bash
cat README.md
```

Check: three new H2 sections appear after the support chart, link definitions are intact at the end.

- [ ] **Step 4: Commit**

```bash
git add README.md
git commit -m "docs: add installation, Symfony Bundle, and Payum integration docs to README"
```

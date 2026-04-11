# Examples & README Docs — Design Spec

**Date:** 2026-04-11

---

## Goal

Add one example PHP file per supported API request (15 new files), and extend `README.md` with Symfony Bundle and Payum integration documentation.

---

## Examples

### Structure

All files live flat in `examples/`, sequentially numbered, following the pattern of the existing `01-echo-request.php`:

```
examples/
  bootstrap.php           (existing, unchanged)
  01-echo-request.php     (existing)
  02-payment-init.php
  03-payment-process.php
  04-payment-status.php
  05-payment-reverse.php
  06-payment-close.php
  07-payment-refund.php
  08-oneclick-echo.php
  09-oneclick-init.php
  10-oneclick-process.php
  11-applepay-echo.php
  12-applepay-init.php
  13-applepay-process.php
  14-googlepay-echo.php
  15-googlepay-init.php
  16-googlepay-process.php
```

### Context-dependency approach

Each file is self-contained and runnable. Where a request requires data from a previous step (e.g. `payId`, `origPayId`, wallet `payload`), the file uses a clearly named variable with a comment explaining its origin. No argument-passing between files.

### File-by-file spec

#### `02-payment-init.php`

`PaymentInitRequest` with a minimal cart item. Outputs `payId`, `resultCode`, `resultMessage`, `paymentStatus`.

Required fields: `merchant`, `orderNumber`, `totalAmount`, `currency`, `returnUrl`, `returnMethod`, `cartItems` (at least one).

#### `03-payment-process.php`

`PaymentProcessRequest::initWith($paymentInitResponse, $merchant)` — does **not** use `send()`. Uses `$client->getPaymentUrlWithPaymentProcessRequest($request)` and prints the redirect URL.

Comment: the `payId` used here comes from the response of `02-payment-init.php`.

#### `04-payment-status.php`

`PaymentStatusRequest` with `merchant` + `payId`. Outputs `payId`, `paymentStatus`, `resultCode`, `resultMessage`.

Comment: `payId` comes from `02-payment-init.php`.

#### `05-payment-reverse.php`

`PaymentReverseRequest` with `merchant` + `payId`. Outputs `resultCode`, `resultMessage`.

Comment: reverses a payment in pending/processing state (paymentStatus 1 or 2). `payId` from `02-payment-init.php`.

#### `06-payment-close.php`

`PaymentCloseRequest` with `merchant` + `payId`. Outputs `resultCode`, `resultMessage`.

Comment: closes an authorized payment (paymentStatus 3 or 5). `payId` from `02-payment-init.php`.

#### `07-payment-refund.php`

`PaymentRefundRequest` with `merchant` + `payId`. Optional `amount` for partial refund — omitting it triggers full refund. Outputs `resultCode`, `resultMessage`.

Comment: `payId` from a captured payment.

#### `08-oneclick-echo.php`

`OneClickEchoRequest` with `merchant`. Outputs `resultCode`, `resultMessage`.

#### `09-oneclick-init.php`

`OneClickInitRequest` with `merchant`, `originalPaymentId`, `orderNumber`, `returnUrl`, `returnMethod`. Outputs `payId`, `resultCode`, `resultMessage`.

Comment: `originalPaymentId` is the `payId` from a previous successful card payment (`payment/init` flow).

#### `10-oneclick-process.php`

`OneClickProcessRequest` with `merchant` + `payId`. Outputs `resultCode`, `resultMessage`, `paymentStatus`.

Comment: `payId` from `09-oneclick-init.php`.

#### `11-applepay-echo.php`

`ApplePayEchoRequest` with `merchant`. Outputs `resultCode`, `resultMessage`.

#### `12-applepay-init.php`

`ApplePayInitRequest` with `merchant`, `orderNumber`, `totalAmount`, `currency`, `payload`, `returnUrl`, `returnMethod`. Outputs `payId`, `resultCode`, `resultMessage`.

Comment: `payload` is the base64-encoded payment token from the Apple Pay JS API (`ApplePaySession.completeMerchantValidation`).

#### `13-applepay-process.php`

`ApplePayProcessRequest` with `merchant` + `payId`. Outputs `resultCode`, `resultMessage`, `paymentStatus`.

Comment: `payId` from `12-applepay-init.php`.

#### `14-googlepay-echo.php`

`GooglePayEchoRequest` with `merchant`. Outputs `resultCode`, `resultMessage`.

#### `15-googlepay-init.php`

`GooglePayInitRequest` with `merchant`, `orderNumber`, `totalAmount`, `currency`, `payload`, `returnUrl`, `returnMethod`. Outputs `payId`, `resultCode`, `resultMessage`.

Comment: `payload` is the JSON payment data from the Google Pay API (`PaymentData.paymentMethodData.tokenizationData.token`).

#### `16-googlepay-process.php`

`GooglePayProcessRequest` with `merchant` + `payId`. Outputs `resultCode`, `resultMessage`, `paymentStatus`.

Comment: `payId` from `15-googlepay-init.php`.

---

## README Updates

Three new sections added after the support chart.

### `## Installation`

```bash
composer require connorhu/khvpos
```

### `## Symfony Bundle`

#### Registering the bundle

No Flex recipe — manual registration required:

```php
// config/bundles.php
return [
    KHTools\VPos\Bundle\KHVPosBundle::class => ['all' => true],
];
```

#### Configuration

```yaml
# config/packages/khvpos.yaml
khvpos:
    test: true   # set to false for production
    merchants:
        default:
            merchant_id: '%env(KHVPOS_MERCHANT_ID)%'
            private_key_path: '%kernel.project_dir%/config/keys/merchant.pem'
            private_key_passphrase: '%env(KHVPOS_PRIVATE_KEY_PASSPHRASE)%'  # optional
            currency: HUF
```

A single `VPosClient` service is registered as `khvpos.vpos_client` (and aliased to `VPosClient::class`). A `MerchantProvider` maps currency codes to merchant IDs — inject `MerchantProviderInterface` to retrieve the correct `Merchant` for a given currency.

```php
public function __construct(
    private VPosClient $client,
    private MerchantProviderInterface $merchantProvider,
) {}

public function pay(string $currency): void
{
    $merchant = $this->merchantProvider->getMerchant($currency); // e.g. 'HUF'
    $request = new PaymentInitRequest();
    $request->setMerchant($merchant);
    // ...
}
```

#### Multiple merchants (multiple currencies)

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

#### All configuration options

| Key | Type | Default | Description |
|---|---|---|---|
| `test` | bool | `true` | Use sandbox endpoint |
| `mips_public_key_path` | string | `null` | MIPS public key path; `null` = bundled key |
| `version` | string | `v1.0` | API version |
| `merchants.<name>.merchant_id` | string | required | K&H merchant ID |
| `merchants.<name>.private_key_path` | string | required | Path to RSA private key PEM |
| `merchants.<name>.private_key_passphrase` | string | `''` | Optional key passphrase |
| `merchants.<name>.currency` | string | `HUF` | Default currency for this merchant |

### `## Payum Integration`

Requires `payum/core ^1.7`. For Symfony, also install `payum/payum-bundle`:

```bash
composer require payum/payum-bundle
```

With `payum/payum-bundle` installed and a `khvpos:` config block present, the `khvpos` Payum gateway is registered automatically — no extra YAML needed:

```php
$gateway = $payum->getGateway('khvpos');
$gateway->execute(new Capture($payment));
```

The first configured merchant is used. Supported Payum requests: `Capture`, `Authorize`, `Refund`, `Cancel`, `Sync`, `GetHumanStatus`, `Convert`.

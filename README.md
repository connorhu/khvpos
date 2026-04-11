
## PHP Library for K&H Payment Gateway

[![Tests status][test status image]][test status] [![Static Analysis][phpstan status image]][phpstan status] [![Coverage Status][2x coverage image]][2x coverage]

[API documentation HU](https://github.com/khpos/Payment-gateway_HU) | [API documentation EN](https://github.com/khpos/Payment-gateway_EN)

## Support chart

| Method            | Support             |
|-------------------|---------------------|
| echo              | [yes][echo example] |
| payment/init      | [yes][payment/init example]      |
| payment/process   | [yes][payment/process example]   |
| payment/status    | [yes][payment/status example]    |
| payment/reverse   | [yes][payment/reverse example]   |
| payment/close     | [yes][payment/close example]     |
| payment/refund    | [yes][payment/refund example]    |
| oneclick/echo     | [yes][oneclick/echo example]     |
| oneclick/init     | [yes][oneclick/init example]     |
| oneclick/process  | [yes][oneclick/process example]  |
| applepay/echo     | [yes][applepay/echo example]     |
| applepay/init     | [yes][applepay/init example]     |
| applepay/process  | [yes][applepay/process example]  |
| googlepay/echo    | [yes][googlepay/echo example]    |
| googlepay/init    | [yes][googlepay/init example]    |
| googlepay/process | [yes][googlepay/process example] |

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
use KHTools\VPos\Requests\PaymentInitRequest;
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

  [test status image]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml/badge.svg?branch=2.x
  [test status]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml
  [phpstan status image]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml/badge.svg
  [phpstan status]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml
  [2x coverage image]: https://codecov.io/gh/connorhu/khvpos/branch/2.x/graph/badge.svg
  [2x coverage]: https://codecov.io/gh/connorhu/khvpos/branch/2.x
  [echo example]: https://github.com/connorhu/khvpos/blob/2.x/examples/01-echo-request.php
  [payment/init example]: https://github.com/connorhu/khvpos/blob/2.x/examples/02-payment-init.php
  [payment/process example]: https://github.com/connorhu/khvpos/blob/2.x/examples/03-payment-process.php
  [payment/status example]: https://github.com/connorhu/khvpos/blob/2.x/examples/04-payment-status.php
  [payment/reverse example]: https://github.com/connorhu/khvpos/blob/2.x/examples/05-payment-reverse.php
  [payment/close example]: https://github.com/connorhu/khvpos/blob/2.x/examples/06-payment-close.php
  [payment/refund example]: https://github.com/connorhu/khvpos/blob/2.x/examples/07-payment-refund.php
  [oneclick/echo example]: https://github.com/connorhu/khvpos/blob/2.x/examples/08-oneclick-echo.php
  [oneclick/init example]: https://github.com/connorhu/khvpos/blob/2.x/examples/09-oneclick-init.php
  [oneclick/process example]: https://github.com/connorhu/khvpos/blob/2.x/examples/10-oneclick-process.php
  [applepay/echo example]: https://github.com/connorhu/khvpos/blob/2.x/examples/11-applepay-echo.php
  [applepay/init example]: https://github.com/connorhu/khvpos/blob/2.x/examples/12-applepay-init.php
  [applepay/process example]: https://github.com/connorhu/khvpos/blob/2.x/examples/13-applepay-process.php
  [googlepay/echo example]: https://github.com/connorhu/khvpos/blob/2.x/examples/14-googlepay-echo.php
  [googlepay/init example]: https://github.com/connorhu/khvpos/blob/2.x/examples/15-googlepay-init.php
  [googlepay/process example]: https://github.com/connorhu/khvpos/blob/2.x/examples/16-googlepay-process.php

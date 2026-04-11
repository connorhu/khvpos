# Endpoint Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement all 9 missing API endpoints (OneClick, ApplePay, GooglePay — echo/init/process), add the missing fields to existing models/requests/responses, and wire everything into `RequestNormalizer` / `ResponseNormalizer` (which by this point accept any class implementing the interfaces from Plan 1).

**Architecture:** This plan depends on Plan 1 (`2026-04-11-refactor-phpstan.md`) being merged first. The OCP refactors in Plan 1 mean new request and response classes are automatically supported by the normalizers — no normalizer changes required here. Each endpoint group (OneClick, ApplePay, GooglePay) follows the same pattern: implement echo (simplest, no payload), then init (full request fields), then process (with fingerprint). Shared response shapes are deduplicated via a common `PaymentActionResponse` base class.

**Tech Stack:** PHP 8.2, PHPUnit 11 (`phpunit-11`), Symfony Serializer 6.4/7.2, `ext-bcmath`

---

## Prerequisites

Plan 1 must be fully merged. Verify:

```bash
phpunit-11 --colors=always
phpstan analyse --memory-limit=1G
```

Both must pass with zero errors before starting this plan.

---

## File Structure

**New files — Models:**
- `src/Models/Fingerprint.php` — browser + SDK fingerprint for 3DS (used by process requests)
- `src/Models/InitParams.php` — ApplePay/GooglePay echo response initParams object

**New files — Requests:**
- `src/Requests/OneClickEchoRequest.php` — replace stub (already has endpoint path)
- `src/Requests/OneClickInitRequest.php` — replace stub
- `src/Requests/OneClickProcessRequest.php` — replace stub
- `src/Requests/ApplePayEchoRequest.php` — replace stub
- `src/Requests/ApplePayInitRequest.php` — replace stub
- `src/Requests/ApplePayProcessRequest.php` — replace stub
- `src/Requests/GooglePayEchoRequest.php` — replace stub
- `src/Requests/GooglePayInitRequest.php` — replace stub
- `src/Requests/GooglePayProcessRequest.php` — replace stub

**New files — Responses:**
- `src/Responses/Traits/PaymentActionResponseTrait.php` — shared payId, paymentStatus, statusDetail, actions fields
- `src/Responses/OneClickEchoResponse.php`
- `src/Responses/OneClickInitResponse.php`
- `src/Responses/OneClickProcessResponse.php`
- `src/Responses/ApplePayEchoResponse.php`
- `src/Responses/ApplePayInitResponse.php`
- `src/Responses/ApplePayProcessResponse.php`
- `src/Responses/GooglePayEchoResponse.php`
- `src/Responses/GooglePayInitResponse.php`
- `src/Responses/GooglePayProcessResponse.php`

**Modified — Missing fields in existing classes:**
- `src/Requests/PaymentInitRequest.php` — add `customExpiry` field
- `src/Responses/PaymentInitResponse.php` — add `customerCode` field
- `src/Models/Order.php` — add `trxUsage` field

**New files — Tests:**
- `tests/Tests/Normalizers/OneClickNormalizerTest.php`
- `tests/Tests/Normalizers/ApplePayNormalizerTest.php`
- `tests/Tests/Normalizers/GooglePayNormalizerTest.php`

---

## Task 1: Add missing fields to existing models/requests/responses

**Files:**
- Modify: `src/Requests/PaymentInitRequest.php`
- Modify: `src/Responses/PaymentInitResponse.php`
- Modify: `src/Models/Order.php`

- [ ] **Step 1: Add `customExpiry` to PaymentInitRequest**

Add after `$ttl` property:

```php
#[SerializedName(serializedName: 'customExpiry')]
private ?string $customExpiry = null;
```

Add getter and setter:

```php
public function getCustomExpiry(): ?string
{
    return $this->customExpiry;
}

public function setCustomExpiry(?string $customExpiry): void
{
    $this->customExpiry = $customExpiry;
}
```

Add `'customExpiry'` to the `NormalizerResultOrderingHelper::ORDER` array in `getNormalizationContext()`, after `'ttlSec'`:

```php
NormalizerResultOrderingHelper::ORDER => [
    'merchantId', 'orderNo', 'dttm', 'payOperation', 'payMethod',
    'totalAmount', 'currency', 'closePayment', 'returnUrl', 'returnMethod',
    'cart', 'customer', 'order', 'merchantData', 'language', 'ttlSec', 'customExpiry',
],
```

- [ ] **Step 2: Add `customerCode` to PaymentInitResponse**

Add after `$statusDetail` property:

```php
private ?string $customerCode = null;
```

Add getter and setter:

```php
public function getCustomerCode(): ?string
{
    return $this->customerCode;
}

public function setCustomerCode(?string $customerCode): void
{
    $this->customerCode = $customerCode;
}
```

Update `getSignatureFieldOrder()` to include `customerCode` (check API docs for exact position — it appears between `paymentStatus` and `statusDetail`):

```php
public static function getSignatureFieldOrder(): array
{
    return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'customerCode', 'statusDetail'];
}
```

- [ ] **Step 3: Add `trxUsage` to Order model**

Add property:

```php
private ?string $trxUsage = null;
```

Add getter and setter:

```php
public function getTrxUsage(): ?string
{
    return $this->trxUsage;
}

public function setTrxUsage(?string $trxUsage): void
{
    $this->trxUsage = $trxUsage;
}
```

- [ ] **Step 4: Run tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 5: Commit**

```bash
git add src/Requests/PaymentInitRequest.php \
  src/Responses/PaymentInitResponse.php \
  src/Models/Order.php
git commit -m "feat: add missing fields customExpiry, customerCode, trxUsage"
```

---

## Task 2: Create shared models (Fingerprint, InitParams)

**Files:**
- Create: `src/Models/Fingerprint.php`
- Create: `src/Models/InitParams.php`

- [ ] **Step 1: Create Fingerprint model**

`src/Models/Fingerprint.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * Device fingerprint data for 3DS authentication.
 * Used in oneclick/process and applepay/process requests.
 */
class Fingerprint
{
    #[SerializedName(serializedName: 'browser')]
    private ?Browser $browserData = null;

    #[SerializedName(serializedName: 'sdk')]
    private ?Sdk $sdkData = null;

    public function getBrowserData(): ?Browser
    {
        return $this->browserData;
    }

    public function setBrowserData(?Browser $browserData): void
    {
        $this->browserData = $browserData;
    }

    public function getSdkData(): ?Sdk
    {
        return $this->sdkData;
    }

    public function setSdkData(?Sdk $sdkData): void
    {
        $this->sdkData = $sdkData;
    }
}
```

- [ ] **Step 2: Create InitParams model**

`src/Models/InitParams.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

/**
 * Initialization parameters returned by applepay/echo and googlepay/echo.
 * The gateway uses these parameters to configure the payment sheet on the client side.
 */
class InitParams
{
    private ?string $merchantIdentifier = null;

    private ?string $merchantName = null;

    private ?string $merchantCountry = null;

    private ?string $supportedNetworks = null;

    public function getMerchantIdentifier(): ?string
    {
        return $this->merchantIdentifier;
    }

    public function setMerchantIdentifier(?string $merchantIdentifier): void
    {
        $this->merchantIdentifier = $merchantIdentifier;
    }

    public function getMerchantName(): ?string
    {
        return $this->merchantName;
    }

    public function setMerchantName(?string $merchantName): void
    {
        $this->merchantName = $merchantName;
    }

    public function getMerchantCountry(): ?string
    {
        return $this->merchantCountry;
    }

    public function setMerchantCountry(?string $merchantCountry): void
    {
        $this->merchantCountry = $merchantCountry;
    }

    public function getSupportedNetworks(): ?string
    {
        return $this->supportedNetworks;
    }

    public function setSupportedNetworks(?string $supportedNetworks): void
    {
        $this->supportedNetworks = $supportedNetworks;
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add src/Models/Fingerprint.php src/Models/InitParams.php
git commit -m "feat: add Fingerprint and InitParams models for new endpoints"
```

---

## Task 3: Create shared PaymentActionResponseTrait

Init, process, and some echo responses all share the same `payId`/`paymentStatus`/`statusDetail`/`actions` structure. Extract this into a trait to avoid duplication.

**Files:**
- Create: `src/Responses/Traits/PaymentActionResponseTrait.php`

- [ ] **Step 1: Create trait**

`src/Responses/Traits/PaymentActionResponseTrait.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses\Traits;

use KHTools\VPos\Models\Authenticate;
use Symfony\Component\Serializer\Annotation\SerializedName;

trait PaymentActionResponseTrait
{
    #[SerializedName(serializedName: 'payId')]
    private string $paymentId;

    private ?int $paymentStatus = null;

    private ?string $statusDetail = null;

    private ?Authenticate $authenticateAction = null;

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentStatus(): ?int
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?int $paymentStatus): void
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function getStatusDetail(): ?string
    {
        return $this->statusDetail;
    }

    public function setStatusDetail(?string $statusDetail): void
    {
        $this->statusDetail = $statusDetail;
    }

    public function getAuthenticateAction(): ?Authenticate
    {
        return $this->authenticateAction;
    }

    public function setAuthenticateAction(?Authenticate $authenticateAction): void
    {
        $this->authenticateAction = $authenticateAction;
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add src/Responses/Traits/PaymentActionResponseTrait.php
git commit -m "feat: add PaymentActionResponseTrait for shared response fields"
```

---

## Task 4: Implement OneClick endpoints

**Files:**
- Modify: `src/Requests/OneClickEchoRequest.php`
- Modify: `src/Requests/OneClickInitRequest.php`
- Modify: `src/Requests/OneClickProcessRequest.php`
- Create: `src/Responses/OneClickEchoResponse.php`
- Create: `src/Responses/OneClickInitResponse.php`
- Create: `src/Responses/OneClickProcessResponse.php`
- Create: `tests/Tests/Normalizers/OneClickNormalizerTest.php`

**API reference:**
- `oneclick/echo` POST: fields `merchantId`, `origPayId`, `dttm`, `signature` → response: `origPayId`, `dttm`, `resultCode`, `resultMessage`
- `oneclick/init` POST: fields `merchantId`, `origPayId`, `orderNo`, `dttm`, `returnUrl`, `returnMethod`, `signature` (plus optional: `clientIp`, `totalAmount`, `currency`, `closePayment`, `customer`, `order`, `clientInitiated`, `sdkUsed`, `merchantData`, `language`, `ttlSec`) → response: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `oneclick/process` POST: fields `merchantId`, `payId`, `dttm`, `signature` (plus optional: `fingerprint`) → response: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`

- [ ] **Step 1: Write failing tests**

Create `tests/Tests/Normalizers/OneClickNormalizerTest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Requests\OneClickProcessRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OneClickNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);

        $this->normalizer = new Serializer([
            new RequestNormalizer($objectNormalizer),
            new CartItemNormalizer($objectNormalizer),
            new EnumNormalizer(),
            new DateTimeNormalizer(),
            $objectNormalizer,
        ]);
    }

    private function merchant(string $id = 'merch01'): Merchant
    {
        $m = new Merchant();
        $m->setMerchantId($id);
        return $m;
    }

    public function testOneClickEchoNormalizesOrigPayId(): void
    {
        $request = new OneClickEchoRequest();
        $request->setMerchant($this->merchant());
        $request->setOriginalPaymentId('pay999');

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertSame('pay999', $result['origPayId']);
        $this->assertArrayHasKey('dttm', $result);
    }

    public function testOneClickInitNormalizesRequiredFields(): void
    {
        $request = new OneClickInitRequest();
        $request->setMerchant($this->merchant());
        $request->setOriginalPaymentId('pay999');
        $request->setOrderNumber('order123');
        $request->setReturnUrl('https://example.com/return');
        $request->setReturnMethod(\KHTools\VPos\Models\Enums\HttpMethod::Post);

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertSame('pay999', $result['origPayId']);
        $this->assertSame('order123', $result['orderNo']);
        $this->assertSame('https://example.com/return', $result['returnUrl']);
        $this->assertArrayHasKey('dttm', $result);
    }

    public function testOneClickProcessNormalizesPayId(): void
    {
        $request = new OneClickProcessRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay123');

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertSame('pay123', $result['payId']);
        $this->assertArrayHasKey('dttm', $result);
    }
}
```

- [ ] **Step 2: Run to confirm tests fail**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/OneClickNormalizerTest.php
```

Expected: FAIL (LogicException from unimplemented stub methods)

- [ ] **Step 3: Create OneClickEchoResponse**

`src/Responses/OneClickEchoResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

class OneClickEchoResponse implements ResponseInterface
{
    use CommonResponseTrait;

    #[SerializedName(serializedName: 'origPayId')]
    private string $originalPaymentId;

    public static function getSignatureFieldOrder(): array
    {
        return ['origPayId', 'dttm', 'resultCode', 'resultMessage'];
    }

    public function getOriginalPaymentId(): string
    {
        return $this->originalPaymentId;
    }

    public function setOriginalPaymentId(string $originalPaymentId): void
    {
        $this->originalPaymentId = $originalPaymentId;
    }
}
```

- [ ] **Step 4: Create OneClickInitResponse and OneClickProcessResponse**

`src/Responses/OneClickInitResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class OneClickInitResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

`src/Responses/OneClickProcessResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class OneClickProcessResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

- [ ] **Step 5: Implement OneClickEchoRequest**

`src/Requests/OneClickEchoRequest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\OneClickEchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class OneClickEchoRequest implements RequestInterface
{
    use MerchantTrait;

    #[SerializedName(serializedName: 'origPayId')]
    private ?string $originalPaymentId = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/oneclick/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return OneClickEchoResponse::class;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId',
                'origPayId',
                'dttm',
            ],
        ];
    }

    public function getOriginalPaymentId(): ?string
    {
        return $this->originalPaymentId;
    }

    public function setOriginalPaymentId(?string $originalPaymentId): void
    {
        $this->originalPaymentId = $originalPaymentId;
    }
}
```

- [ ] **Step 6: Implement OneClickInitRequest**

`src/Requests/OneClickInitRequest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Customer;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Enums\Language;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Models\Order;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\OneClickInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class OneClickInitRequest implements RequestInterface
{
    use MerchantTrait;

    #[SerializedName(serializedName: 'origPayId')]
    private string $originalPaymentId;

    #[SerializedName(serializedName: 'orderNo')]
    private string $orderNumber;

    private string $returnUrl;

    private HttpMethod $returnMethod;

    private ?string $clientIp = null;

    private ?int $totalAmount = null;

    private ?Currency $currency = null;

    private ?bool $closePayment = null;

    private ?Customer $customer = null;

    private ?Order $order = null;

    private ?bool $clientInitiated = null;

    private ?bool $sdkUsed = null;

    private ?string $merchantData = null;

    private ?Language $language = null;

    #[SerializedName(serializedName: 'ttlSec')]
    private ?int $ttl = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/oneclick/init';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return OneClickInitResponse::class;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
                'totalAmount' => function (?int $value): ?int {
                    return $value;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            DateTimeNormalizer::FORMAT_KEY => 'c',
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId', 'origPayId', 'orderNo', 'dttm',
                'returnUrl', 'returnMethod',
                'clientIp', 'totalAmount', 'currency', 'closePayment',
                'customer', 'order', 'clientInitiated', 'sdkUsed',
                'merchantData', 'language', 'ttlSec',
            ],
        ];
    }

    public function getOriginalPaymentId(): string { return $this->originalPaymentId; }
    public function setOriginalPaymentId(string $originalPaymentId): void { $this->originalPaymentId = $originalPaymentId; }

    public function getOrderNumber(): string { return $this->orderNumber; }
    public function setOrderNumber(string $orderNumber): void { $this->orderNumber = $orderNumber; }

    public function getReturnUrl(): string { return $this->returnUrl; }
    public function setReturnUrl(string $returnUrl): void { $this->returnUrl = $returnUrl; }

    public function getReturnMethod(): HttpMethod { return $this->returnMethod; }
    public function setReturnMethod(HttpMethod $returnMethod): void { $this->returnMethod = $returnMethod; }

    public function getClientIp(): ?string { return $this->clientIp; }
    public function setClientIp(?string $clientIp): void { $this->clientIp = $clientIp; }

    public function getTotalAmount(): ?int { return $this->totalAmount; }
    public function setTotalAmount(?int $totalAmount): void { $this->totalAmount = $totalAmount; }

    public function getCurrency(): ?Currency { return $this->currency; }
    public function setCurrency(?Currency $currency): void { $this->currency = $currency; }

    public function getClosePayment(): ?bool { return $this->closePayment; }
    public function setClosePayment(?bool $closePayment): void { $this->closePayment = $closePayment; }

    public function getCustomer(): ?Customer { return $this->customer; }
    public function setCustomer(?Customer $customer): void { $this->customer = $customer; }

    public function getOrder(): ?Order { return $this->order; }
    public function setOrder(?Order $order): void { $this->order = $order; }

    public function getClientInitiated(): ?bool { return $this->clientInitiated; }
    public function setClientInitiated(?bool $clientInitiated): void { $this->clientInitiated = $clientInitiated; }

    public function getSdkUsed(): ?bool { return $this->sdkUsed; }
    public function setSdkUsed(?bool $sdkUsed): void { $this->sdkUsed = $sdkUsed; }

    public function getMerchantData(): ?string { return $this->merchantData; }
    public function setMerchantData(?string $merchantData): void { $this->merchantData = $merchantData; }

    public function getLanguage(): ?Language { return $this->language; }
    public function setLanguage(?Language $language): void { $this->language = $language; }

    public function getTtl(): ?int { return $this->ttl; }
    public function setTtl(?int $ttl): void { $this->ttl = $ttl; }
}
```

- [ ] **Step 7: Implement OneClickProcessRequest**

`src/Requests/OneClickProcessRequest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Fingerprint;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\OneClickProcessResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class OneClickProcessRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    private ?Fingerprint $fingerprint = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/oneclick/process';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return OneClickProcessResponse::class;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId', 'payId', 'dttm', 'fingerprint',
            ],
        ];
    }

    public function getFingerprint(): ?Fingerprint { return $this->fingerprint; }
    public function setFingerprint(?Fingerprint $fingerprint): void { $this->fingerprint = $fingerprint; }
}
```

- [ ] **Step 8: Run OneClick tests**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/OneClickNormalizerTest.php
```

Expected: All PASS

- [ ] **Step 9: Run full test suite**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 10: Commit**

```bash
git add src/Requests/OneClickEchoRequest.php \
  src/Requests/OneClickInitRequest.php \
  src/Requests/OneClickProcessRequest.php \
  src/Responses/OneClickEchoResponse.php \
  src/Responses/OneClickInitResponse.php \
  src/Responses/OneClickProcessResponse.php \
  tests/Tests/Normalizers/OneClickNormalizerTest.php
git commit -m "feat: implement OneClick echo/init/process endpoints"
```

---

## Task 5: Implement ApplePay endpoints

**Files:**
- Modify: `src/Requests/ApplePayEchoRequest.php`
- Modify: `src/Requests/ApplePayInitRequest.php`
- Modify: `src/Requests/ApplePayProcessRequest.php`
- Create: `src/Responses/ApplePayEchoResponse.php`
- Create: `src/Responses/ApplePayInitResponse.php`
- Create: `src/Responses/ApplePayProcessResponse.php`
- Create: `tests/Tests/Normalizers/ApplePayNormalizerTest.php`

**API reference:**
- `applepay/echo` POST: `merchantId`, `dttm`, `signature` → response: `dttm`, `resultCode`, `resultMessage`, `initParams`
- `applepay/init` POST: `merchantId`, `orderNo`, `dttm`, `totalAmount`, `currency`, `payload`, `returnUrl`, `returnMethod`, `signature` (plus optional: `clientIp`, `closePayment`, `customer`, `order`, `sdkUsed`, `merchantData`, `language`, `ttlSec`) → response: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `applepay/process` POST: `merchantId`, `payId`, `dttm`, `signature` (plus optional: `fingerprint`) → same response as process

- [ ] **Step 1: Write failing tests**

Create `tests/Tests/Normalizers/ApplePayNormalizerTest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApplePayNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);

        $this->normalizer = new Serializer([
            new RequestNormalizer($objectNormalizer),
            new CartItemNormalizer($objectNormalizer),
            new EnumNormalizer(),
            new DateTimeNormalizer(),
            $objectNormalizer,
        ]);
    }

    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->setMerchantId('merch01');
        return $m;
    }

    public function testApplePayEchoNormalizesMerchantId(): void
    {
        $request = new ApplePayEchoRequest();
        $request->setMerchant($this->merchant());

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertArrayHasKey('dttm', $result);
        $this->assertArrayNotHasKey('clientIp', $result);
    }

    public function testApplePayInitNormalizesRequiredFields(): void
    {
        $request = new ApplePayInitRequest();
        $request->setMerchant($this->merchant());
        $request->setOrderNumber('ord001');
        $request->setTotalAmount(5000);
        $request->setCurrency(Currency::HUF);
        $request->setPayload('base64payloadhere');
        $request->setReturnUrl('https://example.com/return');
        $request->setReturnMethod(HttpMethod::Post);

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertSame('ord001', $result['orderNo']);
        $this->assertSame(500000, $result['totalAmount']);
        $this->assertSame('HUF', $result['currency']);
        $this->assertSame('base64payloadhere', $result['payload']);
    }

    public function testApplePayProcessNormalizesPayId(): void
    {
        $request = new ApplePayProcessRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay001');

        $result = $this->normalizer->normalize($request);

        $this->assertSame('merch01', $result['merchantId']);
        $this->assertSame('pay001', $result['payId']);
    }
}
```

- [ ] **Step 2: Run to confirm tests fail**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/ApplePayNormalizerTest.php
```

Expected: FAIL

- [ ] **Step 3: Create response classes**

`src/Responses/ApplePayEchoResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Models\InitParams;
use KHTools\VPos\Responses\Traits\CommonResponseTrait;

class ApplePayEchoResponse implements ResponseInterface
{
    use CommonResponseTrait;

    private ?InitParams $initParams = null;

    public static function getSignatureFieldOrder(): array
    {
        return ['dttm', 'resultCode', 'resultMessage', 'initParams'];
    }

    public function getInitParams(): ?InitParams { return $this->initParams; }
    public function setInitParams(?InitParams $initParams): void { $this->initParams = $initParams; }
}
```

`src/Responses/ApplePayInitResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class ApplePayInitResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

`src/Responses/ApplePayProcessResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class ApplePayProcessResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

- [ ] **Step 4: Implement ApplePayEchoRequest**

`src/Requests/ApplePayEchoRequest.php` (replace entire file):

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\ApplePayEchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class ApplePayEchoRequest implements RequestInterface
{
    use MerchantTrait;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/applepay/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return ApplePayEchoResponse::class;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId',
                'dttm',
            ],
        ];
    }
}
```

- [ ] **Step 5: Implement ApplePayInitRequest**

`src/Requests/ApplePayInitRequest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Customer;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Enums\Language;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Models\Order;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\ApplePayInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class ApplePayInitRequest implements RequestInterface
{
    use MerchantTrait;

    #[SerializedName(serializedName: 'orderNo')]
    private string $orderNumber;

    private int $totalAmount;

    private Currency $currency;

    private string $payload;

    private string $returnUrl;

    private HttpMethod $returnMethod;

    private ?string $clientIp = null;

    private ?bool $closePayment = null;

    private ?Customer $customer = null;

    private ?Order $order = null;

    private ?bool $sdkUsed = null;

    private ?string $merchantData = null;

    private ?Language $language = null;

    #[SerializedName(serializedName: 'ttlSec')]
    private ?int $ttl = null;

    #[Ignore]
    public function getRequestMethod(): string { return 'POST'; }

    #[Ignore]
    public function getEndpointPath(): string { return '/applepay/init'; }

    #[Ignore]
    public function getResponseClass(): string { return ApplePayInitResponse::class; }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
                'totalAmount' => function (int $value, ApplePayInitRequest $object): int {
                    return $object->getRawTotalAmount();
                },
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawTotalAmount'],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            DateTimeNormalizer::FORMAT_KEY => 'c',
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId', 'orderNo', 'dttm', 'clientIp',
                'totalAmount', 'currency', 'closePayment', 'payload',
                'returnUrl', 'returnMethod', 'customer', 'order',
                'sdkUsed', 'merchantData', 'language', 'ttlSec',
            ],
        ];
    }

    public function getTotalAmount(): float { return $this->totalAmount / 100; }

    public function getRawTotalAmount(): int { return $this->totalAmount; }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = (int) \bcmul(number_format($totalAmount, 2, '.', ''), '100');
    }

    public function getOrderNumber(): string { return $this->orderNumber; }
    public function setOrderNumber(string $orderNumber): void { $this->orderNumber = $orderNumber; }

    public function getCurrency(): Currency { return $this->currency; }
    public function setCurrency(Currency $currency): void { $this->currency = $currency; }

    public function getPayload(): string { return $this->payload; }
    public function setPayload(string $payload): void { $this->payload = $payload; }

    public function getReturnUrl(): string { return $this->returnUrl; }
    public function setReturnUrl(string $returnUrl): void { $this->returnUrl = $returnUrl; }

    public function getReturnMethod(): HttpMethod { return $this->returnMethod; }
    public function setReturnMethod(HttpMethod $returnMethod): void { $this->returnMethod = $returnMethod; }

    public function getClientIp(): ?string { return $this->clientIp; }
    public function setClientIp(?string $clientIp): void { $this->clientIp = $clientIp; }

    public function getClosePayment(): ?bool { return $this->closePayment; }
    public function setClosePayment(?bool $closePayment): void { $this->closePayment = $closePayment; }

    public function getCustomer(): ?Customer { return $this->customer; }
    public function setCustomer(?Customer $customer): void { $this->customer = $customer; }

    public function getOrder(): ?Order { return $this->order; }
    public function setOrder(?Order $order): void { $this->order = $order; }

    public function getSdkUsed(): ?bool { return $this->sdkUsed; }
    public function setSdkUsed(?bool $sdkUsed): void { $this->sdkUsed = $sdkUsed; }

    public function getMerchantData(): ?string { return $this->merchantData; }
    public function setMerchantData(?string $merchantData): void { $this->merchantData = $merchantData; }

    public function getLanguage(): ?Language { return $this->language; }
    public function setLanguage(?Language $language): void { $this->language = $language; }

    public function getTtl(): ?int { return $this->ttl; }
    public function setTtl(?int $ttl): void { $this->ttl = $ttl; }
}
```

- [ ] **Step 6: Implement ApplePayProcessRequest**

`src/Requests/ApplePayProcessRequest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Fingerprint;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\ApplePayProcessResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class ApplePayProcessRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    private ?Fingerprint $fingerprint = null;

    #[Ignore]
    public function getRequestMethod(): string { return 'POST'; }

    #[Ignore]
    public function getEndpointPath(): string { return '/applepay/process'; }

    #[Ignore]
    public function getResponseClass(): string { return ApplePayProcessResponse::class; }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId', 'payId', 'dttm', 'fingerprint',
            ],
        ];
    }

    public function getFingerprint(): ?Fingerprint { return $this->fingerprint; }
    public function setFingerprint(?Fingerprint $fingerprint): void { $this->fingerprint = $fingerprint; }
}
```

- [ ] **Step 7: Run ApplePay tests**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/ApplePayNormalizerTest.php
```

Expected: All PASS

- [ ] **Step 8: Run full suite**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 9: Commit**

```bash
git add src/Requests/ApplePayEchoRequest.php \
  src/Requests/ApplePayInitRequest.php \
  src/Requests/ApplePayProcessRequest.php \
  src/Responses/ApplePayEchoResponse.php \
  src/Responses/ApplePayInitResponse.php \
  src/Responses/ApplePayProcessResponse.php \
  tests/Tests/Normalizers/ApplePayNormalizerTest.php
git commit -m "feat: implement ApplePay echo/init/process endpoints"
```

---

## Task 6: Implement GooglePay endpoints

**Files:**
- Modify: `src/Requests/GooglePayEchoRequest.php`
- Modify: `src/Requests/GooglePayInitRequest.php`
- Modify: `src/Requests/GooglePayProcessRequest.php`
- Create: `src/Responses/GooglePayEchoResponse.php`
- Create: `src/Responses/GooglePayInitResponse.php`
- Create: `src/Responses/GooglePayProcessResponse.php`
- Create: `tests/Tests/Normalizers/GooglePayNormalizerTest.php`

**API reference:**
- `googlepay/echo` POST: identical to `applepay/echo` (same fields, same response shape with `initParams`)
- `googlepay/init` POST: same fields as `applepay/init`; endpoint `/googlepay/init`
- `googlepay/process` POST: same as `applepay/process`; endpoint `/googlepay/process`

- [ ] **Step 1: Write failing tests**

Create `tests/Tests/Normalizers/GooglePayNormalizerTest.php` — identical structure to `ApplePayNormalizerTest` but using `GooglePayEchoRequest`, `GooglePayInitRequest`, `GooglePayProcessRequest`. Duplicate the test class, substitute the class names, and assert `result['merchantId']`, `result['orderNo']`, `result['totalAmount']`, `result['payload']`, `result['payId']` as appropriate.

- [ ] **Step 2: Run to confirm tests fail**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/GooglePayNormalizerTest.php
```

Expected: FAIL

- [ ] **Step 3: Create response classes**

`src/Responses/GooglePayEchoResponse.php` — identical to `ApplePayEchoResponse` but class name changed:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Models\InitParams;
use KHTools\VPos\Responses\Traits\CommonResponseTrait;

class GooglePayEchoResponse implements ResponseInterface
{
    use CommonResponseTrait;

    private ?InitParams $initParams = null;

    public static function getSignatureFieldOrder(): array
    {
        return ['dttm', 'resultCode', 'resultMessage', 'initParams'];
    }

    public function getInitParams(): ?InitParams { return $this->initParams; }
    public function setInitParams(?InitParams $initParams): void { $this->initParams = $initParams; }
}
```

`src/Responses/GooglePayInitResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class GooglePayInitResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

`src/Responses/GooglePayProcessResponse.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class GooglePayProcessResponse implements ResponseInterface
{
    use CommonResponseTrait;
    use PaymentActionResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
    }
}
```

- [ ] **Step 4: Implement GooglePayEchoRequest**

`src/Requests/GooglePayEchoRequest.php` — identical to `ApplePayEchoRequest` with class name and endpoint changed:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\GooglePayEchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class GooglePayEchoRequest implements RequestInterface
{
    use MerchantTrait;

    #[Ignore]
    public function getRequestMethod(): string { return 'POST'; }

    #[Ignore]
    public function getEndpointPath(): string { return '/googlepay/echo'; }

    #[Ignore]
    public function getResponseClass(): string { return GooglePayEchoResponse::class; }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => ['merchantId', 'dttm'],
        ];
    }
}
```

- [ ] **Step 5: Implement GooglePayInitRequest**

`src/Requests/GooglePayInitRequest.php` — identical to `ApplePayInitRequest` except:
- class name: `GooglePayInitRequest`
- endpoint: `/googlepay/init`
- response class: `GooglePayInitResponse::class`

Copy `ApplePayInitRequest.php`, substitute those three values, and adjust the `getNormalizationContext()` `ORDER` array per API docs (same fields as ApplePay init).

- [ ] **Step 6: Implement GooglePayProcessRequest**

`src/Requests/GooglePayProcessRequest.php` — identical to `ApplePayProcessRequest` except class name, endpoint (`/googlepay/process`), and response class (`GooglePayProcessResponse::class`).

- [ ] **Step 7: Run GooglePay tests**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/GooglePayNormalizerTest.php
```

Expected: All PASS

- [ ] **Step 8: Run full suite and PHPStan**

```bash
phpunit-11 --colors=always
phpstan analyse --memory-limit=1G
```

Expected: All PASS, zero PHPStan errors

- [ ] **Step 9: Commit**

```bash
git add src/Requests/GooglePayEchoRequest.php \
  src/Requests/GooglePayInitRequest.php \
  src/Requests/GooglePayProcessRequest.php \
  src/Responses/GooglePayEchoResponse.php \
  src/Responses/GooglePayInitResponse.php \
  src/Responses/GooglePayProcessResponse.php \
  tests/Tests/Normalizers/GooglePayNormalizerTest.php
git commit -m "feat: implement GooglePay echo/init/process endpoints"
```

---

## Task 7: Update README support chart

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Update support table**

In `README.md`, update all `no` entries to `yes`:

```markdown
| oneclick/echo     | yes                 |
| oneclick/init     | yes                 |
| oneclick/process  | yes                 |
| applepay/echo     | yes                 |
| applepay/init     | yes                 |
| applepay/process  | yes                 |
| googlepay/echo    | yes                 |
| googlepay/init    | yes                 |
| googlepay/process | yes                 |
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: mark all endpoints as implemented in support chart"
```

---

## Self-Review

**Spec coverage check:**

| Spec item | Covered by task |
|-----------|----------------|
| OneClickEchoRequest + OneClickEchoResponse | Task 4 |
| OneClickInitRequest + OneClickInitResponse | Task 4 |
| OneClickProcessRequest + OneClickProcessResponse | Task 4 |
| ApplePayEchoResponse (with initParams) | Task 5 |
| ApplePayInitRequest/Response | Task 5 |
| ApplePayProcessRequest/Response | Task 5 |
| GooglePayEchoResponse (with initParams) | Task 6 |
| GooglePayInitRequest/Response | Task 6 |
| GooglePayProcessRequest/Response | Task 6 |
| customExpiry in PaymentInitRequest | Task 1 |
| customerCode in PaymentInitResponse | Task 1 |
| trxUsage in Order | Task 1 |
| GiftCard typed object | Covered in Plan 1 Task 8 |

**Type consistency:** `getSignatureFieldOrder()` is static across all response classes (consistent with Plan 1). `getNormalizationContext()` is instance method with `#[Ignore]` across all request classes (consistent with Plan 1). `getRawTotalAmount()` / `setTotalAmount()` pattern mirrors existing `PaymentInitRequest`.

**No placeholders found.**

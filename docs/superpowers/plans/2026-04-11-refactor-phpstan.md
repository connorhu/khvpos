# Refactor + PHPStan Hardening Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix all clean-code problems identified in the audit spec (C1, C2, M1–M4, N1–N4) and harden the project with strict PHPStan checks (P1–P4), leaving the codebase ready for clean endpoint implementation.

**Architecture:** Tasks are ordered so each is independently testable: critical safety fixes first (C1/C2), then OCP refactors (M1/M2) which require touching every request/response class anyway, then model/setter cleanup (M3/M4/N1), then misc fixes (N2–N4), finally PHPStan tooling (P1–P4). Existing tests act as regression protection throughout.

**Tech Stack:** PHP 8.2, PHPUnit 11 (`phpunit-11`), PHPStan 1.x (`phpstan`), Symfony Serializer 6.4/7.2, `ext-bcmath`

---

## File Structure

**Modified:**
- `src/Requests/RequestInterface.php` — add `getNormalizationContext(): array`
- `src/Requests/EchoRequest.php` — implement `getNormalizationContext()`, add `#[Ignore]`
- `src/Requests/PaymentInitRequest.php` — same
- `src/Requests/PaymentStatusRequest.php` — same
- `src/Requests/PaymentProcessRequest.php` — same
- `src/Requests/PaymentReverseRequest.php` — same
- `src/Requests/PaymentCloseRequest.php` — same
- `src/Requests/PaymentRefundRequest.php` — same; also fix `setAmount()` to use bcmath
- `src/Requests/OneClickInitRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/OneClickEchoRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/OneClickProcessRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/ApplePayInitRequest.php` — same
- `src/Requests/ApplePayEchoRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/ApplePayProcessRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/GooglePayInitRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/GooglePayEchoRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Requests/GooglePayProcessRequest.php` — exit → LogicException, add `getNormalizationContext()`
- `src/Normalizers/RequestNormalizer.php` — remove `getContextWithObject()`, call `getNormalizationContext()`
- `src/Responses/ResponseInterface.php` — add `getSignatureFieldOrder(): array`
- `src/Responses/EchoResponse.php` — implement `getSignatureFieldOrder()`
- `src/Responses/PaymentInitResponse.php` — same
- `src/Responses/PaymentReverseResponse.php` — same
- `src/Responses/PaymentStatusResponse.php` — same
- `src/Responses/PaymentProcessResponse.php` — same
- `src/Responses/PaymentCloseResponse.php` — same
- `src/Responses/PaymentRefundResponse.php` — same
- `src/Responses/Traits/CommonResponseTrait.php` — fix `?int`/`?string` return types
- `src/Normalizers/ResponseNormalizer.php` — remove `responseKeyOrderWithClass()`, use `getSignatureFieldOrder()`
- `src/Models/CartItem.php` — fix `setAmount()` to use bcmath; setters → void
- `src/Models/Customer.php` — properties → private; setters → void
- `src/Models/Order.php` — properties → private; `$giftCards` → `array<int, GiftCard>`
- `src/Models/Authenticate.php` — setters → void
- `src/Normalizers/NormalizerResultOrderingHelper.php` — remove unused imports
- `src/VPosClient.php` — add return type on `send()`; consolidate 403 handling
- `composer.json` — add phpstan-strict-rules, phpstan-symfony
- `phpstan.neon.dist` — add extensions
- `tests/StaticAnalysis/phpstan.baseline.php` — cleared after fixes
- `tests/Tests/Normalizers/RequestNormalizerTest.php` — update Customer direct property access to use setters

**Created:**
- `src/Models/GiftCard.php` — typed GiftCard model
- `tests/Tests/Requests/StubRequestTest.php` — LogicException assertions for unimplemented stubs
- `tests/Tests/Responses/CommonResponseTraitTest.php` — assert `getResultCode()` returns null before deserialization

---

## Task 1: C1 — Replace `exit` with `LogicException` in stub classes

**Files:**
- Modify: `src/Requests/OneClickInitRequest.php`, `src/Requests/OneClickEchoRequest.php`, `src/Requests/OneClickProcessRequest.php`, `src/Requests/ApplePayInitRequest.php`, `src/Requests/ApplePayEchoRequest.php`, `src/Requests/ApplePayProcessRequest.php`, `src/Requests/GooglePayInitRequest.php`, `src/Requests/GooglePayEchoRequest.php`, `src/Requests/GooglePayProcessRequest.php`
- Create: `tests/Tests/Requests/StubRequestTest.php`

- [ ] **Step 1: Write the failing tests**

Create `tests/Tests/Requests/StubRequestTest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\Tests\Requests;

use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use KHTools\VPos\Requests\GooglePayEchoRequest;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Requests\GooglePayProcessRequest;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Requests\OneClickProcessRequest;
use PHPUnit\Framework\TestCase;

class StubRequestTest extends TestCase
{
    /** @return array<string, array{class-string}> */
    public static function stubClassProvider(): array
    {
        return [
            'OneClickInitRequest' => [OneClickInitRequest::class],
            'OneClickProcessRequest' => [OneClickProcessRequest::class],
            'ApplePayInitRequest' => [ApplePayInitRequest::class],
            'ApplePayProcessRequest' => [ApplePayProcessRequest::class],
            'GooglePayInitRequest' => [GooglePayInitRequest::class],
            'GooglePayProcessRequest' => [GooglePayProcessRequest::class],
        ];
    }

    /** @dataProvider stubClassProvider */
    public function testGetEndpointPathThrowsLogicException(string $class): void
    {
        $request = new $class();
        $this->expectException(\LogicException::class);
        $request->getEndpointPath();
    }

    /** @return array<string, array{class-string}> */
    public static function allStubClassProvider(): array
    {
        return [
            'OneClickInitRequest' => [OneClickInitRequest::class],
            'OneClickEchoRequest' => [OneClickEchoRequest::class],
            'OneClickProcessRequest' => [OneClickProcessRequest::class],
            'ApplePayInitRequest' => [ApplePayInitRequest::class],
            'ApplePayEchoRequest' => [ApplePayEchoRequest::class],
            'ApplePayProcessRequest' => [ApplePayProcessRequest::class],
            'GooglePayInitRequest' => [GooglePayInitRequest::class],
            'GooglePayEchoRequest' => [GooglePayEchoRequest::class],
            'GooglePayProcessRequest' => [GooglePayProcessRequest::class],
        ];
    }

    /** @dataProvider allStubClassProvider */
    public function testGetResponseClassThrowsLogicException(string $class): void
    {
        $request = new $class();
        $this->expectException(\LogicException::class);
        $request->getResponseClass();
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
phpunit-11 --colors=always tests/Tests/Requests/StubRequestTest.php
```

Expected: FAIL (tests currently kill the process via `exit`)

- [ ] **Step 3: Fix OneClickInitRequest and OneClickProcessRequest**

`src/Requests/OneClickInitRequest.php` — replace both `exit;` blocks:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/OneClickProcessRequest.php` — same change:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/OneClickEchoRequest.php` — only `getResponseClass()` has `exit`:
```php
#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

- [ ] **Step 4: Fix ApplePay stubs**

`src/Requests/ApplePayInitRequest.php`:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/ApplePayEchoRequest.php` — only `getResponseClass()`:
```php
#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/ApplePayProcessRequest.php`:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

- [ ] **Step 5: Fix GooglePay stubs**

`src/Requests/GooglePayInitRequest.php`:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/GooglePayEchoRequest.php` — only `getResponseClass()`:
```php
#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

`src/Requests/GooglePayProcessRequest.php`:
```php
#[Ignore]
public function getEndpointPath(): string
{
    throw new \LogicException('Not yet implemented');
}

#[Ignore]
public function getResponseClass(): string
{
    throw new \LogicException('Not yet implemented');
}
```

- [ ] **Step 6: Run the new tests**

```bash
phpunit-11 --colors=always tests/Tests/Requests/StubRequestTest.php
```

Expected: All PASS

- [ ] **Step 7: Run the full test suite**

```bash
phpunit-11 --colors=always
```

Expected: All existing tests PASS (no regressions)

- [ ] **Step 8: Commit**

```bash
git add tests/Tests/Requests/StubRequestTest.php \
  src/Requests/OneClickInitRequest.php \
  src/Requests/OneClickEchoRequest.php \
  src/Requests/OneClickProcessRequest.php \
  src/Requests/ApplePayInitRequest.php \
  src/Requests/ApplePayEchoRequest.php \
  src/Requests/ApplePayProcessRequest.php \
  src/Requests/GooglePayInitRequest.php \
  src/Requests/GooglePayEchoRequest.php \
  src/Requests/GooglePayProcessRequest.php
git commit -m "fix: replace exit with LogicException in unimplemented stub classes"
```

---

## Task 2: C2 — Fix nullable type mismatch in CommonResponseTrait

**Files:**
- Modify: `src/Responses/Traits/CommonResponseTrait.php`
- Modify: `src/Responses/ResponseInterface.php`
- Create: `tests/Tests/Responses/CommonResponseTraitTest.php`

- [ ] **Step 1: Write failing test**

Create `tests/Tests/Responses/CommonResponseTraitTest.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\Tests\Responses;

use KHTools\VPos\Responses\EchoResponse;
use PHPUnit\Framework\TestCase;

class CommonResponseTraitTest extends TestCase
{
    public function testGetResultCodeReturnsNullBeforeDeserialization(): void
    {
        $response = new EchoResponse();
        $this->assertNull($response->getResultCode());
    }

    public function testGetResultMessageReturnsNullBeforeDeserialization(): void
    {
        $response = new EchoResponse();
        $this->assertNull($response->getResultMessage());
    }

    public function testGetResultCodeReturnsIntAfterSet(): void
    {
        $response = new EchoResponse();
        $response->setResultCode(0);
        $this->assertSame(0, $response->getResultCode());
    }

    public function testGetResultMessageReturnsStringAfterSet(): void
    {
        $response = new EchoResponse();
        $response->setResultMessage('OK');
        $this->assertSame('OK', $response->getResultMessage());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
phpunit-11 --colors=always tests/Tests/Responses/CommonResponseTraitTest.php
```

Expected: FAIL — `testGetResultCodeReturnsNullBeforeDeserialization` fails with `TypeError` because `getResultCode()` declares `int` return but the property is `null`.

- [ ] **Step 3: Update ResponseInterface**

`src/Responses/ResponseInterface.php`:
```php
<?php

namespace KHTools\VPos\Responses;

interface ResponseInterface
{
    public function getResultCode(): ?int;

    public function getResultMessage(): ?string;
}
```

- [ ] **Step 4: Update CommonResponseTrait**

`src/Responses/Traits/CommonResponseTrait.php`:
```php
<?php

namespace KHTools\VPos\Responses\Traits;

trait CommonResponseTrait
{
    private ?int $resultCode = null;

    private ?string $resultMessage = null;

    public function getResultCode(): ?int
    {
        return $this->resultCode;
    }

    public function setResultCode(int $resultCode): void
    {
        $this->resultCode = $resultCode;
    }

    public function getResultMessage(): ?string
    {
        return $this->resultMessage;
    }

    public function setResultMessage(string $resultMessage): void
    {
        $this->resultMessage = $resultMessage;
    }
}
```

- [ ] **Step 5: Run tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 6: Commit**

```bash
git add src/Responses/ResponseInterface.php \
  src/Responses/Traits/CommonResponseTrait.php \
  tests/Tests/Responses/CommonResponseTraitTest.php
git commit -m "fix: make getResultCode/getResultMessage return nullable types"
```

---

## Task 3: M1 — Add getNormalizationContext() to all request classes

**Files:**
- Modify: `src/Requests/RequestInterface.php`
- Modify: `src/Requests/EchoRequest.php`
- Modify: `src/Requests/PaymentInitRequest.php`
- Modify: `src/Requests/PaymentStatusRequest.php`
- Modify: `src/Requests/PaymentProcessRequest.php`
- Modify: `src/Requests/PaymentReverseRequest.php`
- Modify: `src/Requests/PaymentCloseRequest.php`
- Modify: `src/Requests/PaymentRefundRequest.php`
- Modify all 9 stub classes from Task 1

Note: The existing `RequestNormalizerTest` is the regression guard — do NOT modify it in this task.

- [ ] **Step 1: Add method to RequestInterface**

`src/Requests/RequestInterface.php`:
```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;

interface RequestInterface
{
    public function getRequestMethod(): string;

    public function getEndpointPath(): string;

    public function getMerchant(): Merchant;

    public function setMerchant(Merchant $merchant): void;

    /**
     * @return class-string
     */
    public function getResponseClass(): string;

    /**
     * Returns the Symfony Serializer normalizer context to use when serializing this request.
     * Implementations must annotate this method with #[Ignore] to prevent it from appearing
     * in the serialized output.
     *
     * @return array<string, mixed>
     */
    public function getNormalizationContext(): array;
}
```

- [ ] **Step 2: Implement in EchoRequest**

Add to `src/Requests/EchoRequest.php` (add necessary imports and method):
```php
use KHTools\VPos\Models\Merchant;
use Symfony\Component\Serializer\Annotation\Ignore;

// Add this method to the class:
#[Ignore]
public function getNormalizationContext(): array
{
    return [
        \Symfony\Component\Serializer\Normalizer\AbstractNormalizer::CALLBACKS => [
            'merchant' => function (Merchant $value): string {
                return $value->merchantId;
            },
        ],
    ];
}
```

Full updated `src/Requests/EchoRequest.php`:
```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\EchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class EchoRequest implements RequestInterface
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
        return '/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return EchoResponse::class;
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
        ];
    }
}
```

- [ ] **Step 3: Implement in PaymentInitRequest**

Add to `src/Requests/PaymentInitRequest.php`. Add necessary imports at the top (keep all existing ones, add the missing ones), then add the method:

```php
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;

// Add this method to the class body:
#[Ignore]
public function getNormalizationContext(): array
{
    return [
        AbstractNormalizer::CALLBACKS => [
            'totalAmount' => function (float $value, PaymentInitRequest $object): int {
                return $object->getRawTotalAmount();
            },
            'merchant' => function (Merchant $value): string {
                return $value->merchantId;
            },
        ],
        AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawTotalAmount'],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        DateTimeNormalizer::FORMAT_KEY => 'c',
        NormalizerResultOrderingHelper::ORDER => [
            'merchantId',
            'orderNo',
            'dttm',
            'payOperation',
            'payMethod',
            'totalAmount',
            'currency',
            'closePayment',
            'returnUrl',
            'returnMethod',
            'cart',
            'customer',
            'order',
            'merchantData',
            'language',
            'ttlSec',
        ],
    ];
}
```

- [ ] **Step 4: Implement in PaymentStatusRequest, PaymentProcessRequest, PaymentReverseRequest**

All three share the same context. Add to each:

```php
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

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
            'payId',
            'dttm',
        ],
    ];
}
```

- [ ] **Step 5: Implement in PaymentCloseRequest**

Add to `src/Requests/PaymentCloseRequest.php`:

```php
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Ignore]
public function getNormalizationContext(): array
{
    return [
        AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawTotalAmount'],
        AbstractNormalizer::CALLBACKS => [
            'totalAmount' => function (mixed $value, PaymentCloseRequest $object): ?int {
                return $object->getRawTotalAmount();
            },
        ],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        NormalizerResultOrderingHelper::ORDER => [
            'merchantId',
            'payId',
            'dttm',
            'totalAmount',
        ],
    ];
}
```

- [ ] **Step 6: Implement in PaymentRefundRequest**

Add to `src/Requests/PaymentRefundRequest.php`:

```php
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Ignore]
public function getNormalizationContext(): array
{
    return [
        AbstractNormalizer::CALLBACKS => [
            'merchant' => function (Merchant $value): string {
                return $value->merchantId;
            },
            'amount' => function (mixed $value, PaymentRefundRequest $object): ?int {
                return $object->getRawAmount();
            },
        ],
        AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawAmount'],
        AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        NormalizerResultOrderingHelper::ORDER => [
            'merchantId',
            'payId',
            'dttm',
            'amount',
        ],
    ];
}
```

- [ ] **Step 7: Add LogicException stubs for all 9 unimplemented request classes**

Each of the 9 stubs (OneClickInitRequest, OneClickEchoRequest, OneClickProcessRequest, ApplePayInitRequest, ApplePayEchoRequest, ApplePayProcessRequest, GooglePayInitRequest, GooglePayEchoRequest, GooglePayProcessRequest) needs:

```php
#[Ignore]
public function getNormalizationContext(): array
{
    throw new \LogicException('Not yet implemented');
}
```

Add this method to each class (no additional imports needed — `\LogicException` is a PHP built-in).

- [ ] **Step 8: Run tests to verify no regressions**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 9: Commit**

```bash
git add src/Requests/RequestInterface.php \
  src/Requests/EchoRequest.php \
  src/Requests/PaymentInitRequest.php \
  src/Requests/PaymentStatusRequest.php \
  src/Requests/PaymentProcessRequest.php \
  src/Requests/PaymentReverseRequest.php \
  src/Requests/PaymentCloseRequest.php \
  src/Requests/PaymentRefundRequest.php \
  src/Requests/OneClickInitRequest.php \
  src/Requests/OneClickEchoRequest.php \
  src/Requests/OneClickProcessRequest.php \
  src/Requests/ApplePayInitRequest.php \
  src/Requests/ApplePayEchoRequest.php \
  src/Requests/ApplePayProcessRequest.php \
  src/Requests/GooglePayInitRequest.php \
  src/Requests/GooglePayEchoRequest.php \
  src/Requests/GooglePayProcessRequest.php
git commit -m "refactor: add getNormalizationContext() to RequestInterface and all implementations"
```

---

## Task 4: M1 — Refactor RequestNormalizer to remove the if-elseif chain

**Files:**
- Modify: `src/Normalizers/RequestNormalizer.php`

The `RequestNormalizerTest` remains the regression guard — it must still pass without changes.

- [ ] **Step 1: Run existing normalizer tests to establish baseline**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/RequestNormalizerTest.php
```

Expected: All PASS

- [ ] **Step 2: Replace RequestNormalizer**

`src/Normalizers/RequestNormalizer.php` — full replacement:

```php
<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Requests\RequestInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RequestNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer,
    ) {
    }

    /** @return array<string, mixed> */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $context = $object->getNormalizationContext();
        $normalized = $this->objectNormalizer->normalize($object, $format, $context);
        $normalized['dttm'] = date('YmdHis');

        if (isset($context[NormalizerResultOrderingHelper::ORDER])) {
            $normalized = NormalizerResultOrderingHelper::orderArray($normalized, $context[NormalizerResultOrderingHelper::ORDER]);
        }

        if (isset($normalized['order']['giftcards']) && count($normalized['order']['giftcards']) === 0) {
            unset($normalized['order']['giftcards']);
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof RequestInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            RequestInterface::class => true,
        ];
    }
}
```

- [ ] **Step 3: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 4: Commit**

```bash
git add src/Normalizers/RequestNormalizer.php
git commit -m "refactor: RequestNormalizer delegates context to each request class (OCP)"
```

---

## Task 5: M2 — Add getSignatureFieldOrder() to all response classes

**Files:**
- Modify: `src/Responses/ResponseInterface.php`
- Modify: `src/Responses/EchoResponse.php`
- Modify: `src/Responses/PaymentInitResponse.php`
- Modify: `src/Responses/PaymentReverseResponse.php`
- Modify: `src/Responses/PaymentStatusResponse.php`
- Modify: `src/Responses/PaymentProcessResponse.php`
- Modify: `src/Responses/PaymentCloseResponse.php`
- Modify: `src/Responses/PaymentRefundResponse.php`

The `ResponseNormalizerTest` remains the regression guard.

- [ ] **Step 1: Add static method to ResponseInterface**

`src/Responses/ResponseInterface.php`:
```php
<?php

namespace KHTools\VPos\Responses;

interface ResponseInterface
{
    public function getResultCode(): ?int;

    public function getResultMessage(): ?string;

    /**
     * Returns the field names in the order they must appear when building the signature string.
     * This order is defined by the K&H API documentation for each response type.
     *
     * @return list<string>
     */
    public static function getSignatureFieldOrder(): array;
}
```

- [ ] **Step 2: Implement in EchoResponse**

`src/Responses/EchoResponse.php`:
```php
<?php

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;

class EchoResponse implements ResponseInterface
{
    use CommonResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['dttm', 'resultCode', 'resultMessage'];
    }
}
```

- [ ] **Step 3: Implement in PaymentInitResponse and PaymentReverseResponse**

Both share the same field order. Add to each class:

```php
public static function getSignatureFieldOrder(): array
{
    return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail'];
}
```

- [ ] **Step 4: Implement in PaymentStatusResponse**

Add to `src/Responses/PaymentStatusResponse.php`:

```php
public static function getSignatureFieldOrder(): array
{
    return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'authCode', 'statusDetail', 'actions'];
}
```

- [ ] **Step 5: Implement in PaymentProcessResponse**

Add to `src/Responses/PaymentProcessResponse.php`:

```php
public static function getSignatureFieldOrder(): array
{
    return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'authCode', 'merchantData', 'statusDetail'];
}
```

- [ ] **Step 6: Implement in PaymentCloseResponse and PaymentRefundResponse**

Both share the same field order. Add to each class:

```php
public static function getSignatureFieldOrder(): array
{
    return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'authCode', 'statusDetail'];
}
```

- [ ] **Step 7: Run tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 8: Commit**

```bash
git add src/Responses/ResponseInterface.php \
  src/Responses/EchoResponse.php \
  src/Responses/PaymentInitResponse.php \
  src/Responses/PaymentReverseResponse.php \
  src/Responses/PaymentStatusResponse.php \
  src/Responses/PaymentProcessResponse.php \
  src/Responses/PaymentCloseResponse.php \
  src/Responses/PaymentRefundResponse.php
git commit -m "refactor: add getSignatureFieldOrder() to ResponseInterface and all implementations"
```

---

## Task 6: M2 — Refactor ResponseNormalizer to remove the match statement

**Files:**
- Modify: `src/Normalizers/ResponseNormalizer.php`

- [ ] **Step 1: Run existing normalizer tests to establish baseline**

```bash
phpunit-11 --colors=always tests/Tests/Normalizers/ResponseNormalizerTest.php
```

Expected: All PASS

- [ ] **Step 2: Replace ResponseNormalizer**

`src/Normalizers/ResponseNormalizer.php` — full replacement:

```php
<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\Authenticate;
use KHTools\VPos\Exceptions\VerificationFailedException;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\Responses\ResponseInterface;
use KHTools\VPos\SignatureProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ResponseNormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly SignatureProviderInterface $signatureProvider,
        private readonly ObjectNormalizer $objectNormalizer,
    ) {
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): object
    {
        $signature = $data['signature'] ?? null;
        $data = NormalizerResultOrderingHelper::orderArray($data, $type::getSignatureFieldOrder());

        if ($signature !== null) {
            $verificationResult = $this->signatureProvider->verify($data, $signature);
            if ($verificationResult === false) {
                throw new VerificationFailedException();
            }
        }

        $object = $this->objectNormalizer->denormalize($data, $type, $format);

        if ($object instanceof PaymentStatusResponse && isset($data['actions'])) {
            if (isset($data['actions']['authenticate'])) {
                $authenticate = $this->objectNormalizer->denormalize($data['actions']['authenticate'], Authenticate::class, 'array');
                $object->setAuthenticateAction($authenticate);
            }
        }

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, ResponseInterface::class, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            ResponseInterface::class => true,
        ];
    }
}
```

Note: `supportsDenormalization()` now uses `is_a($type, ResponseInterface::class, true)` instead of the previous expression which accessed `class_implements()` return value with an offset — this also fixes the PHPStan baseline error about offset access on `array|false`.

- [ ] **Step 3: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 4: Commit**

```bash
git add src/Normalizers/ResponseNormalizer.php
git commit -m "refactor: ResponseNormalizer delegates field order to each response class (OCP)"
```

---

## Task 7: M3 — Fix currency conversion to use bcmath consistently

**Files:**
- Modify: `src/Models/CartItem.php`
- Modify: `src/Requests/PaymentRefundRequest.php`

Both currently use `round()` for float-to-integer currency conversion. `round()` is unreliable for monetary values due to IEEE 754 floating-point representation. The correct approach uses `bcmath` as established in `PaymentInitRequest::setTotalAmount()`.

- [ ] **Step 1: Run existing CartItem tests to establish baseline**

```bash
phpunit-11 --colors=always tests/Tests/Models/CartItemTest.php
```

Expected: All PASS

- [ ] **Step 2: Fix CartItem::setAmount()**

In `src/Models/CartItem.php`, replace the `setAmount()` method body:

```php
public function setAmount(?float $amount): self
{
    $this->amount = (int) \bcmul(number_format((float) $amount, 2, '.', ''), '100');
    return $this;
}
```

- [ ] **Step 3: Fix PaymentRefundRequest::setAmount()**

In `src/Requests/PaymentRefundRequest.php`, replace the `setAmount()` method body:

```php
public function setAmount(?float $amount): void
{
    $this->amount = (int) \bcmul(number_format((float) $amount, 2, '.', ''), '100');
}
```

- [ ] **Step 4: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 5: Commit**

```bash
git add src/Models/CartItem.php src/Requests/PaymentRefundRequest.php
git commit -m "fix: use bcmath for float-to-integer currency conversion in CartItem and PaymentRefundRequest"
```

---

## Task 8: M4 — Make Customer and Order properties private; add GiftCard model

**Files:**
- Modify: `src/Models/Customer.php`
- Modify: `src/Models/Order.php`
- Create: `src/Models/GiftCard.php`
- Modify: `tests/Tests/Normalizers/RequestNormalizerTest.php`

`Customer` and `Order` declare `public` properties alongside full getters/setters. The tests access these properties directly — they need updating to use the setters.

- [ ] **Step 1: Identify all direct property accesses in tests**

The `RequestNormalizerTest` uses:
```php
$customer->account = new CustomerAccount();
$customer->login = new CustomerLogin();
$customer->name = 'name of the customer';
$customer->account->changedAt = new \DateTimeImmutable('2023-01-01T04:05:06+00:00');
$customer->login->auth = CustomerLoginAuth::Api;
```

These must become setter calls. `CustomerAccount` and `CustomerLogin` also have public properties — check and update similarly if needed.

- [ ] **Step 2: Read CustomerAccount and CustomerLogin**

```bash
cat src/Models/CustomerAccount.php
cat src/Models/CustomerLogin.php
```

Note which properties are public and need setters.

- [ ] **Step 3: Create GiftCard model**

Create `src/Models/GiftCard.php`:

```php
<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\Currency;

class GiftCard
{
    private ?int $totalAmount = null;

    private ?Currency $currency = null;

    private ?int $quantity = null;

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
```

- [ ] **Step 4: Update Order to use private properties and typed GiftCard array**

In `src/Models/Order.php`, change all `public` property declarations to `private`, and update `$giftCards`:

```php
private ?OrderType $type = null;
private ?OrderAvailability $availability = null;
private ?OrderDelivery $delivery = null;
private ?DeliveryMode $deliveryMode = null;
private ?string $deliveryEmail = null;
private ?bool $nameMatch = null;
private ?bool $addressMatch = null;
private ?Address $billing = null;
private ?Address $shipping = null;
private ?\DateTime $shippingAddedAt = null;
private ?bool $reorder = null;

/** @var array<int, GiftCard> */
#[SerializedName(serializedName: 'giftcards')]
private array $giftCards = [];
```

Add `use KHTools\VPos\Models\GiftCard;` import. Update the `getGiftCards()` return type and `setGiftCards()` parameter type:

```php
/** @return array<int, GiftCard> */
public function getGiftCards(): array
{
    return $this->giftCards;
}

/** @param array<int, GiftCard> $giftCards */
public function setGiftCards(array $giftCards): void
{
    $this->giftCards = $giftCards;
}
```

- [ ] **Step 5: Update Customer to use private properties**

In `src/Models/Customer.php`, change all `public` property declarations to `private`:

```php
private ?string $name = null;
private ?string $email = null;
private ?string $homePhone = null;
private ?string $workPhone = null;
private ?string $mobilePhone = null;
private ?CustomerAccount $account = null;
private ?CustomerLogin $login = null;
```

- [ ] **Step 6: Update CustomerAccount and CustomerLogin if needed**

Inspect the classes. If they have `public` properties, make them `private` and ensure getters/setters exist for all properties used in tests (`changedAt`, `auth`).

- [ ] **Step 7: Update RequestNormalizerTest to use setters**

In `tests/Tests/Normalizers/RequestNormalizerTest.php`, replace direct property access with setter calls. Find the block (around line 193–211) and replace:

```php
// Old (direct property access):
$customer = new Customer();
$customer->account = new CustomerAccount();
$customer->account->changedAt = new \DateTimeImmutable('2023-01-01T04:05:06+00:00');
$customer->login = new CustomerLogin();
$customer->login->auth = CustomerLoginAuth::Api;
$customer->name = 'name of the customer';

// New (use setters):
$customerAccount = new CustomerAccount();
$customerAccount->setChangedAt(new \DateTimeImmutable('2023-01-01T04:05:06+00:00'));

$customerLogin = new CustomerLogin();
$customerLogin->setAuth(CustomerLoginAuth::Api);

$customer = new Customer();
$customer->setAccount($customerAccount);
$customer->setLogin($customerLogin);
$customer->setName('name of the customer');
```

Also check if `CustomerAccount` and `CustomerLogin` are used elsewhere in tests via direct property access.

- [ ] **Step 8: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 9: Commit**

```bash
git add src/Models/Customer.php \
  src/Models/Order.php \
  src/Models/GiftCard.php \
  tests/Tests/Normalizers/RequestNormalizerTest.php
git commit -m "refactor: make Customer/Order properties private; add typed GiftCard model"
```

---

## Task 9: N1 — Standardize setters to void return type

**Files:**
- Modify: `src/Models/CartItem.php`
- Modify: `src/Models/Customer.php`
- Modify: `src/Models/Authenticate.php`

`Merchant::setMerchantId()` returns `static` and is used fluently in tests — leave it unchanged.

- [ ] **Step 1: Update CartItem setters**

In `src/Models/CartItem.php`, change `setName()`, `setQuantity()`, `setAmount()`, `setDescription()` from `self` return type to `void`, and remove `return $this;` from each:

```php
public function setName(?string $name): void
{
    $this->name = $name;
}

public function setQuantity(?int $quantity): void
{
    $this->quantity = $quantity;
}

public function setAmount(?float $amount): void
{
    $this->amount = (int) \bcmul(number_format((float) $amount, 2, '.', ''), '100');
}

public function setDescription(?string $description): void
{
    $this->description = $description;
}
```

- [ ] **Step 2: Update Customer setters**

In `src/Models/Customer.php`, change all setters (`setName`, `setEmail`, `setHomePhone`, `setWorkPhone`, `setMobilePhone`, `setAccount`, `setLogin`) from `self` return type to `void`, and remove `return $this;` from each.

- [ ] **Step 3: Update Authenticate setters**

In `src/Models/Authenticate.php`, change `setBrowserChallenge()` and `setSdkChallenge()` from `self` return type to `void`, and remove `return $this;` from each:

```php
public function setBrowserChallenge(?Browser $browserChallenge): void
{
    $this->browserChallenge = $browserChallenge;
}

public function setSdkChallenge(?Sdk $sdkChallenge): void
{
    $this->sdkChallenge = $sdkChallenge;
}
```

- [ ] **Step 4: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS (no test chains these setters)

- [ ] **Step 5: Commit**

```bash
git add src/Models/CartItem.php src/Models/Customer.php src/Models/Authenticate.php
git commit -m "refactor: standardize model setters to void return type"
```

---

## Task 10: N2/N3/N4 — VPosClient return type, dead imports, 403 consolidation

**Files:**
- Modify: `src/VPosClient.php`
- Modify: `src/Normalizers/NormalizerResultOrderingHelper.php`

- [ ] **Step 1: Add return type to VPosClient::send()**

In `src/VPosClient.php`, update the `send()` signature:

```php
public function send(RequestInterface $request): ResponseInterface
```

Add `use KHTools\VPos\Responses\ResponseInterface;` import if not already present.

- [ ] **Step 2: Consolidate 403 handling in VPosClient::send()**

Currently the code checks 403 twice. Replace the error-handling block in `send()` with consolidated logic:

```php
if (($statusCode = $response->getStatusCode()) !== 200) {
    $contentType = $response->getHeaders()['content-type'][0] ?? '';

    if ($contentType !== 'application/json') {
        throw new ClientErrorException($response->getBody()->getContents(), $statusCode);
    }

    $responseClass = HttpErrorException::getErrorClassWithResponseCode($statusCode);
} else {
    $responseClass = $request->getResponseClass();
}
```

This removes the special-case `$statusCode === 403 && $contentType !== 'application/json'` branch — all non-JSON error responses are treated as `ClientErrorException` regardless of status code, which is consistent behavior.

- [ ] **Step 3: Remove unused imports from NormalizerResultOrderingHelper**

`src/Normalizers/NormalizerResultOrderingHelper.php` — remove all unused imports. The class only needs no imports (it uses only built-in PHP types):

```php
<?php

namespace KHTools\VPos\Normalizers;

class NormalizerResultOrderingHelper
{
    public const ORDER = '__order';

    /**
     * @param array<string, mixed> $arrayToOrder
     * @param list<string> $keyOrder
     * @return array<string, mixed>
     */
    public static function orderArray(array $arrayToOrder, array $keyOrder): array
    {
        $buffer = [];
        foreach ($keyOrder as $key) {
            if (!isset($arrayToOrder[$key])) {
                continue;
            }

            $buffer[$key] = $arrayToOrder[$key];
        }

        return $buffer;
    }
}
```

- [ ] **Step 4: Run all tests**

```bash
phpunit-11 --colors=always
```

Expected: All PASS

- [ ] **Step 5: Commit**

```bash
git add src/VPosClient.php src/Normalizers/NormalizerResultOrderingHelper.php
git commit -m "refactor: add return type to send(), consolidate 403 handling, remove dead imports"
```

---

## Task 11: P1/P2/P3/P4 — PHPStan hardening

**Files:**
- Modify: `composer.json`
- Modify: `phpstan.neon.dist`
- Modify: `tests/StaticAnalysis/phpstan.baseline.php`
- Create: `phpstan-tests.neon.dist`

- [ ] **Step 1: Install new phpstan packages**

```bash
composer require --dev phpstan/phpstan-strict-rules phpstan/phpstan-symfony
```

Expected: Both packages installed without dependency conflicts.

- [ ] **Step 2: Update phpstan.neon.dist**

`phpstan.neon.dist`:
```neon
parameters:
    level: 8
    phpVersion: 80200
    paths:
        - src

includes:
    - tests/StaticAnalysis/phpstan.baseline.php
    - vendor/phpstan/phpstan-strict-rules/rules.neon
    - vendor/phpstan/phpstan-symfony/extension.neon
```

- [ ] **Step 3: Run phpstan to see what errors remain**

```bash
phpstan analyse --memory-limit=1G
```

Note all remaining errors. These fall into two categories:
- Errors that should be fixed in code (prefer this)
- Errors that are false positives or require accepted suppression (add to baseline)

- [ ] **Step 4: Fix remaining fixable errors**

Work through the errors one by one. Common remaining errors after the earlier tasks:

- `$normalized` in `RequestNormalizer` from `objectNormalizer->normalize()` returns `array|ArrayObject|...` — cast it: `$normalized = (array) $this->objectNormalizer->normalize($object, $format, $context);`
- Any remaining untyped `array` parameters in `SignatureProvider` — add `@param array<string, mixed>` docblocks
- `VPosClient::prepareEndpointPath()` returns `string|null` but declares `string` — add a null check or `?? throw`

Fix each one, then re-run phpstan after each fix.

- [ ] **Step 5: Clear the baseline**

`tests/StaticAnalysis/phpstan.baseline.php`:
```php
<?php declare(strict_types = 1);

$ignoreErrors = [];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
```

- [ ] **Step 6: Run phpstan again**

```bash
phpstan analyse --memory-limit=1G
```

Expected: `[OK] No errors` or only intentionally suppressed items. If new errors appear, fix them or add them back to baseline with a comment explaining why.

- [ ] **Step 7: Create phpstan-tests.neon.dist for test analysis**

Create `phpstan-tests.neon.dist`:
```neon
parameters:
    level: 5
    phpVersion: 80200
    paths:
        - tests/Tests

includes:
    - vendor/phpstan/phpstan-symfony/extension.neon
```

- [ ] **Step 8: Add test analysis script to composer.json**

In `composer.json`, add to `scripts`:
```json
"phpstan-tests": "phpstan analyse --memory-limit=1G -c phpstan-tests.neon.dist",
"analyze": [
    "@phpstan",
    "@phpstan-tests"
]
```

- [ ] **Step 9: Run test analysis**

```bash
composer phpstan-tests
```

Fix any errors found in `tests/Tests/`.

- [ ] **Step 10: Run full suite one last time**

```bash
phpunit-11 --colors=always
composer phpstan
composer phpstan-tests
```

Expected: All pass with zero errors.

- [ ] **Step 11: Commit**

```bash
git add composer.json composer.lock phpstan.neon.dist phpstan-tests.neon.dist \
  tests/StaticAnalysis/phpstan.baseline.php
git commit -m "build: add phpstan-strict-rules and phpstan-symfony; clear baseline"
```

---

## Self-Review

**Spec coverage check:**

| Spec item | Covered by task |
|-----------|----------------|
| C1 exit → LogicException | Task 1 |
| C2 nullable type mismatch | Task 2 |
| M1 RequestNormalizer OCP | Tasks 3–4 |
| M2 ResponseNormalizer OCP | Tasks 5–6 |
| M3 CartItem bcmath | Task 7 |
| M3 PaymentRefundRequest bcmath | Task 7 |
| M4 Customer private props | Task 8 |
| M4 Order private props + GiftCard | Task 8 |
| N1 void setters (CartItem, Customer, Authenticate) | Task 9 |
| N2 VPosClient::send() return type | Task 10 |
| N3 NormalizerResultOrderingHelper dead imports | Task 10 |
| N4 403 consolidation | Task 10 |
| P1 clean baseline | Task 11 |
| P2 phpstan-strict-rules | Task 11 |
| P3 phpstan-symfony | Task 11 |
| P4 tests/ analysis | Task 11 |

**No placeholders found.** All steps contain actual code or explicit commands.

**Type consistency:** `getNormalizationContext()` is instance method throughout (Tasks 3–4); `getSignatureFieldOrder()` is static throughout (Tasks 5–6). `ResponseNormalizer` calls `$type::getSignatureFieldOrder()` matching the static declaration. `send()` return type is `ResponseInterface` matching the Responses namespace.

# Coverage Improvement — Design Spec

**Date:** 2026-04-11

---

## Goal

Increase meaningful test coverage by adding real, behaviour-verifying tests. Not metric inflation — every test must catch a real bug if the implementation is wrong.

## Current State

Library tests (excluding Bundle): **~52% line coverage, 37% method coverage** (117 tests).

Biggest uncovered areas:
- `SignatureProvider` — 0% (core RSA signing / verification logic)
- `VPosClient` — 0% (main entry point: signing, normalizing, HTTP, response deserialization)
- Request classes — 25–50% (`getEndpointPath()`, `getRequestMethod()`, `getResponseClass()` never called in tests)
- `RequestNormalizer::supportsNormalization()` / `ResponseNormalizer::supportsDenormalization()` — uncalled

## Approach

Four tasks, bottom-up by complexity. Pure unit tests first, integration last.

---

## Task 1: SignatureProvider unit tests

**File:** `tests/Tests/SignatureProviderTest.php`

**Fixtures already present:** `tests/Tests/Fixtures/test1_private_key.pem`, `tests/Tests/Fixtures/test1_public_key.pem`, `tests/Tests/Fixtures/test2_private_key.pem`, `tests/Tests/Fixtures/test2_public_key.pem`

### Test cases

**`sign()` produces a valid RSA-SHA256 signature**

```php
$provider = new SignatureProvider(
    ['M123' => new PrivateKey(__DIR__.'/../Fixtures/test1_private_key.pem')],
    __DIR__.'/../Fixtures/test1_public_key.pem',
);
$merchant = new Merchant();
$merchant->merchantId = 'M123';
$payload = ['merchantId' => 'M123', 'dttm' => '20240101120000', 'orderNo' => '1001'];

$signature = $provider->sign($merchant, $payload);

$decoded = base64_decode($signature, strict: true);
$pubKey = openssl_pkey_get_public(file_get_contents(__DIR__.'/../Fixtures/test1_public_key.pem'));
$result = openssl_verify('M123|20240101120000|1001', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
$this->assertSame(1, $result);
```

**`sign()` accepts a private key path string via `addPrivateKey()`**

Call `addPrivateKey('M123', '/path/to/key.pem')` (string, not PrivateKey object) — verify it loads lazily and `sign()` succeeds without error.

**`verify()` returns true for correctly signed content**

Sign a payload with `test1_private_key.pem`. Construct a new `SignatureProvider` with `test1_public_key.pem` as the MIPS key. Call `verify($payload, $signature)` → `true`.

**`verify()` returns false for tampered content**

Sign a payload, then modify one field value. `verify()` → `false`.

**`verify()` throws `SSLErrorException` for invalid base64 signature**

Pass `'not-base64!!'` as the signature → `SSLErrorException`.

**`buildStringContentToSign` behaviour (tested indirectly via `sign()` + manual `openssl_verify`)**

Test cases that verify the string-building rules:

- Boolean `true` serializes as `'true'`, `false` as `'false'`
- The `'signature'` key is excluded from the signed string
- Nested arrays are flattened recursively (values concatenated with `|`)

For each case: call `sign()`, then verify the signature against the manually constructed expected string using `openssl_verify()` directly — if the expected string was wrong, verification fails.

---

## Task 2: Request metadata tests

**File:** `tests/Tests/Requests/RequestMetadataTest.php`

Single data-driven test class. One data provider row per concrete request class.

### Data provider

| Class | Method | Path | Response class |
|---|---|---|---|
| `EchoRequest` | `POST` | `/echo` | `EchoResponse` |
| `PaymentInitRequest` | `POST` | `/payment/init` | `PaymentInitResponse` |
| `PaymentStatusRequest` | `GET` | `/payment/status/{merchantId}/{payId}/{dttm}/{signature}` | `PaymentStatusResponse` |
| `PaymentProcessRequest` | `GET` | `/payment/process/{merchantId}/{payId}/{dttm}/{signature}` | *(throws)* |
| `PaymentCloseRequest` | `PUT` | `/payment/close` | `PaymentCloseResponse` |
| `PaymentReverseRequest` | `PUT` | `/payment/reverse` | `PaymentReverseResponse` |
| `PaymentRefundRequest` | `PUT` | `/payment/refund` | `PaymentRefundResponse` |
| `OneClickEchoRequest` | `POST` | `/oneclick/echo` | `OneClickEchoResponse` |
| `OneClickInitRequest` | `POST` | `/oneclick/init` | `OneClickInitResponse` |
| `OneClickProcessRequest` | `POST` | `/oneclick/process` | `OneClickProcessResponse` |
| `ApplePayEchoRequest` | `POST` | `/applepay/echo` | `ApplePayEchoResponse` |
| `ApplePayInitRequest` | `POST` | `/applepay/init` | `ApplePayInitResponse` |
| `ApplePayProcessRequest` | `POST` | `/applepay/process` | `ApplePayProcessResponse` |
| `GooglePayEchoRequest` | `POST` | `/googlepay/echo` | `GooglePayEchoResponse` |
| `GooglePayInitRequest` | `POST` | `/googlepay/init` | `GooglePayInitResponse` |
| `GooglePayProcessRequest` | `POST` | `/googlepay/process` | `GooglePayProcessResponse` |

### Test methods

**`testRequestMethod(string $class, string $expectedMethod)`**
Instantiate the class with `new $class()`, assert `getRequestMethod() === $expectedMethod`.

**`testEndpointPath(string $class, string $expectedPath)`**
Same instantiation, assert `getEndpointPath() === $expectedPath`.

**`testResponseClass(string $class, string $expectedResponseClass)`**
Assert `getResponseClass() === $expectedResponseClass` AND that `$expectedResponseClass` is a class implementing `ResponseInterface`.

**`testPaymentProcessRequestResponseClassThrows()`**
Separate test (not data-driven): `(new PaymentProcessRequest())->getResponseClass()` → `\BadFunctionCallException`.

---

## Task 3: Normalizer support method tests

**Files:** extend existing `tests/Tests/Normalizers/RequestNormalizerTest.php` and `tests/Tests/Normalizers/ResponseNormalizerTest.php`

### `RequestNormalizerTest` additions

```php
public function testSupportsNormalizationForRequestInterface(): void
{
    $normalizer = new RequestNormalizer($this->objectNormalizer());
    $this->assertTrue($normalizer->supportsNormalization(new PaymentInitRequest()));
}

public function testSupportsNormalizationReturnsFalseForOther(): void
{
    $normalizer = new RequestNormalizer($this->objectNormalizer());
    $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
}
```

`objectNormalizer()` is an extraction of the `setUp()` factory code into a helper method.

### `ResponseNormalizerTest` additions

Extract the `ResponseNormalizer` construction from `setUp()` into a private helper `buildResponseNormalizer(): ResponseNormalizer`, then use it in the new tests:

```php
public function testSupportsDenormalizationForResponseInterface(): void
{
    $normalizer = $this->buildResponseNormalizer();
    $this->assertTrue($normalizer->supportsDenormalization([], PaymentStatusResponse::class));
}

public function testSupportsDenormalizationReturnsFalseForOther(): void
{
    $normalizer = $this->buildResponseNormalizer();
    $this->assertFalse($normalizer->supportsDenormalization([], \stdClass::class));
}
```

---

## Task 4: VPosClient integration tests

**File:** `tests/Tests/VPosClientTest.php`

### Container setup

`ServiceSubscriberTrait` reads services via `$this->container->get(__METHOD__)` where `__METHOD__` is the fully-qualified method name. The container must satisfy these keys:

```
KHTools\VPos\VPosClient::getSignatureProvider  → SignatureProviderInterface
KHTools\VPos\VPosClient::getNormalizer         → NormalizerInterface
KHTools\VPos\VPosClient::getDenormalizer       → DenormalizerInterface
KHTools\VPos\VPosClient::getSerializer         → SerializerInterface
KHTools\VPos\VPosClient::getHttpClient         → Psr\Http\Client\ClientInterface
KHTools\VPos\VPosClient::getRequestFactory     → RequestFactoryInterface
KHTools\VPos\VPosClient::getStreamFactory      → StreamFactoryInterface
```

Use a minimal PSR container (anonymous class implementing `Psr\Container\ContainerInterface`) populated in `setUp()`.

PSR-18 mock HTTP client: `Symfony\Component\HttpClient\MockHttpClient` + `Symfony\Component\HttpClient\Psr18Client` adapter (already in `require-dev`).

Real services:
- `SignatureProvider` with `test1_private_key.pem` (merchant ID `'M123'`) and `test1_public_key.pem` as MIPS key
- Full Symfony `Serializer` with all 6 normalizers + `ResponseNormalizer` + `RequestNormalizer`

A `buildClient(callable $mockResponseFactory): VPosClient` helper method builds the client with the given mock HTTP behaviour.

### Test cases

**`testSendPostRequest()` — PaymentInitRequest → PaymentInitResponse**

Mock HTTP returns a canned JSON matching the `PaymentInitResponse` signature field order (`payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `authCode`). Call `$client->send($request)`, assert the response object has correct field values. No signature verification in the mock response (SignatureProvider in `ResponseNormalizer` uses the MIPS public key, which won't match a test-signed response — pass an empty `signature` field; the ResponseNormalizer skips verification when signature is absent or when SignatureProvider is constructed with an empty MIPS key path).

**`testSendGetRequest()` — PaymentStatusRequest**

Set `payId` on the request, call `send()`. Capture the PSR-7 request URI the mock received — assert it contains the `payId` in the path and `signature` is URL-encoded (no `+` or `=` unencoded).

**`testSendThrowsOnHttpError()`**

Mock returns HTTP 400 with `Content-Type: application/json` body. Assert a `HttpErrorException` subclass is thrown.

**`testGetPaymentUrlWithPaymentProcessRequest()`**

Call `getPaymentUrlWithPaymentProcessRequest($request)`. Assert the returned URL:
- Starts with `https://api.khpos.hu/api/v1.0/payment/process/`
- Contains the `payId` in the path
- Contains a URL-encoded `signature` query parameter

**`testGetPaymentUrlUsesTestEndpointWhenTestModeEnabled()`**

Construct `VPosClient` with `isTest: true`. Assert the URL starts with `https://api.sandbox.khpos.hu/api/v1.0/`.

**`testInitResponseWithArray()`**

Pass a raw array to `initResponseWithArray($array, PaymentStatusResponse::class)`. Assert the returned object is a `PaymentStatusResponse` with the correct field values. No HTTP involved.

---

## Notes

- `PaymentProcessRequest::getResponseClass()` intentionally throws `BadFunctionCallException` — its use via `VPosClient` goes through `getPaymentUrlWithPaymentProcessRequest()` not `send()`, so no response class is needed.
- `ResponseNormalizer` signature verification: when `verify()` would fail against a non-MIPS-signed test response, pass a `SignatureProvider` constructed with the same test key pair for both signing and verification, or use a `SignatureProvider` subclass that skips verification. The simplest approach: sign the canned response JSON with `test1_private_key.pem` in the test setup and use `test1_public_key.pem` as the MIPS key — making the verification pass end-to-end.

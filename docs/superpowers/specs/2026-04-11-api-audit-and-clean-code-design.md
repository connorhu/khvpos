# API Audit & Clean Code — Design Spec

**Date:** 2026-04-11  
**Approach:** Fix first, then build — a refactor section precedes the implementation section because several clean code problems directly obstruct clean endpoint implementation.

---

## Part 1: API Implementation Gaps

### 1.1 Completely empty stubs (with `exit` calls)

These classes have `exit;` in both `getEndpointPath()` and `getResponseClass()`:

| Class | Endpoint |
|-------|----------|
| `OneClickInitRequest` | `POST /oneclick/init` |
| `ApplePayInitRequest` | `POST /applepay/init` |
| `GooglePayInitRequest` | `POST /googlepay/init` |

### 1.2 Partially implemented stubs

Endpoint path is set, but `getResponseClass()` contains `exit;`:

| Class | Endpoint |
|-------|----------|
| `OneClickEchoRequest` | `POST /oneclick/echo` |
| `OneClickProcessRequest` | `POST /oneclick/process` |
| `ApplePayEchoRequest` | `POST /applepay/echo` |
| `ApplePayProcessRequest` | `POST /applepay/process` |
| `GooglePayEchoRequest` | `POST /googlepay/echo` |
| `GooglePayProcessRequest` | `POST /googlepay/process` |

### 1.3 Missing response classes

None of the following exist:

- `OneClickEchoResponse` — fields: `origPayId`, `dttm`, `resultCode`, `resultMessage`
- `OneClickInitResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `OneClickProcessResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `ApplePayEchoResponse` — fields: `dttm`, `resultCode`, `resultMessage`, `initParams` (object)
- `ApplePayInitResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `ApplePayProcessResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `GooglePayEchoResponse` — fields: `dttm`, `resultCode`, `resultMessage`, `initParams` (object)
- `GooglePayInitResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`
- `GooglePayProcessResponse` — fields: `payId`, `dttm`, `resultCode`, `resultMessage`, `paymentStatus`, `statusDetail`, `actions`

### 1.4 Missing fields in existing implementations

| Location | Missing field | Notes |
|----------|--------------|-------|
| `PaymentInitRequest` | `customExpiry` | Optional string |
| `PaymentInitResponse` | `customerCode` | Optional string |
| `Order` model | `trxUsage` | Optional string, values: `crypto`, `NFT` |
| `Order.giftCards` | Typed object | Currently `?array`; should be a `GiftCard` model with `totalAmount` (int), `currency` (string), `quantity` (int) |

---

## Part 2: Refactor (Clean Code)

### Critical

**C1 — `exit` calls in stub classes**  
*Files:* `OneClickInitRequest`, `ApplePayInitRequest`, `GooglePayInitRequest` (and partially the other 6 stubs)  
*Problem:* `exit;` in interface method implementations terminates the PHP process if called accidentally (e.g. via DI container).  
*Fix:* Replace all `exit;` with `throw new \LogicException('Not implemented')`.

**C2 — Nullable type mismatch in `CommonResponseTrait`**  
*File:* `src/Responses/Traits/CommonResponseTrait.php`  
*Problem:* `private ?int $resultCode = null` is declared nullable, but `getResultCode(): int` has a non-nullable return type. Calling this before deserialization causes a `TypeError` at runtime. PHPStan baseline currently suppresses this.  
*Fix:* Either change the property to `private int $resultCode` with a default value, or change the return type to `?int`.

---

### Medium

**M1 — `RequestNormalizer` violates Open/Closed Principle**  
*File:* `src/Normalizers/RequestNormalizer.php`  
*Problem:* `getContextWithObject()` is a large `if-elseif` chain that must be modified for every new request type. Adding 9 new endpoints here makes this method unmanageable.  
*Fix:* Add a `getNormalizationContext(): array` method to `RequestInterface`. Each request class returns its own context. `RequestNormalizer` calls this method generically — no more branching.

**M2 — `ResponseNormalizer` violates Open/Closed Principle**  
*File:* `src/Normalizers/ResponseNormalizer.php`  
*Problem:* `responseKeyOrderWithClass()` is a `match` statement that must be updated for every new response type.  
*Fix:* Add a `getSignatureFieldOrder(): array` method to `ResponseInterface` (instance or static — what matters is that `ResponseNormalizer` can call it generically without a `match`). Each response class returns its own field order.

**M3 — Inconsistent currency conversion**  
*Files:* `src/Models/CartItem.php`, `src/Requests/PaymentInitRequest.php`  
*Problem:* `CartItem::setAmount()` uses `round()` while `PaymentInitRequest::setTotalAmount()` uses `bcmath`. The `bcmath` approach is correct for avoiding floating point precision loss; `round()` is not reliable for currency.  
*Fix:* Update `CartItem::setAmount()` to use `bcmath` (same pattern as `PaymentInitRequest`).

**M4 — Public properties mixed with getters/setters**  
*Files:* `src/Models/Customer.php`, `src/Models/Order.php`  
*Problem:* Properties are declared `public` but getters and setters also exist — encapsulation is broken with no benefit. `Order::$giftCards` is `public ?array` alongside `getGiftCards()`/`setGiftCards()`.  
*Fix:* Change all properties in these classes to `private`. The getters/setters remain the only access point.

---

### Nice-to-have

**N1 — Inconsistent setter return types**  
`CartItem`, `Customer`, `Authenticate` setters return `self` (fluent); `Order`, `PaymentInitRequest` setters return `void`.  
*Fix:* Standardise to `void` across all model/request setters. Fluent setters are uncommon in PHP library conventions and the Symfony serializer does not require them.

**N2 — `VPosClient::send()` missing return type**  
*Fix:* Add `ResponseInterface` as the return type (or `ResponseInterface|\Exception` to reflect the error path, though throwing is preferable).

**N3 — Unused imports in `NormalizerResultOrderingHelper`**  
`Address`, `ObjectNormalizer`, `NormalizerInterface`, `AbstractObjectNormalizer` are imported but unused.  
*Fix:* Remove the dead imports.

**N4 — 403 status code handled in two places**  
`HttpErrorException::getErrorClassWithResponseCode()` maps 403 → `ClientErrorException`, but `VPosClient::send()` also has an explicit `if ($statusCode === 403 && $contentType !== 'application/json')` branch above it. The logic is split.  
*Fix:* Move the content-type check into `HttpErrorException` or consolidate in `VPosClient::send()` before delegating to `HttpErrorException`.

---

## Part 3: PHPStan

**P1 — Clean the baseline**  
`tests/StaticAnalysis/phpstan.baseline.php` currently suppresses known errors, making level 8 weaker than it appears. After the critical fixes (C1, C2) are applied, re-run PHPStan and empty the baseline. Any remaining suppressed errors should either be fixed or explicitly justified with a comment.

**P2 — Add `phpstan/phpstan-strict-rules`**  
Adds: mandatory explicit return types, forbidden `isset()` on typed properties, null-safe `count()`, and more. Appropriate for a library codebase where consumers depend on the type contracts.

**P3 — Add `phpstan/phpstan-symfony` extension**  
The project uses `#[SubscribedService]` DI attributes and Symfony Serializer conventions. Without this extension, PHPStan generates false positives on service container resolution and serializer callbacks.

**P4 — Include `tests/` in analysis**  
Currently PHPStan only analyses `src/`. Adding `tests/` with a relaxed level (e.g. level 5, separate config) catches type errors in test setup code.

---

## Implementation Order (if proceeding)

1. **C1, C2** — critical fixes (unblock everything else, clean the baseline)
2. **M1, M2** — OCP refactor in normalizers (must precede adding new endpoints)
3. **M3, M4** — model fixes
4. **P1, P2, P3** — PHPStan hardening
5. **Part 1** — implement missing endpoints and response classes
6. **N1–N4** — nice-to-have cleanup
7. **P4** — test analysis

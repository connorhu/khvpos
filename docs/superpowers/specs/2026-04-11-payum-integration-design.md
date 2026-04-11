# Payum Integration — Design Spec

**Date:** 2026-04-11

---

## Goal

Add an optional Payum payment processing integration. Framework-agnostic Actions and a GatewayFactory live in `src/Payum/`; Symfony users get automatic wiring when `payum/payum-bundle` is present, with no extra config required beyond the existing `khvpos:` YAML block.

---

## Approach

Two layers, same package:

1. **`src/Payum/`** — `payum/core`-only dependency. `KHVPosGatewayFactory` + six Action classes wrap `VPosClient`. Works in any PHP environment.
2. **`src/Bundle/DependencyInjection/Compiler/PayumGatewayPass.php`** — Symfony CompilerPass. Detects `payum/payum-bundle` at container build time and auto-registers the gateway factory and all Actions as services.

`VPosClient` is injected into every Action. Its `ServiceSubscriberTrait` ensures dependencies (HTTP, Serializer, SignatureProvider) are lazy — nothing initialises until the first actual payment operation.

---

## File Structure

| File | Action | Responsibility |
|---|---|---|
| `src/Payum/KHVPosGatewayFactory.php` | Create | `GatewayFactoryInterface` implementation; builds `VPosClient` + registers Actions |
| `src/Payum/Constants.php` | Create | K&H `paymentStatus` integer constants with Payum human status mapping |
| `src/Payum/Action/ConvertPaymentAction.php` | Create | Payum `Payment` model → `PaymentInitRequest` fields |
| `src/Payum/Action/CaptureAction.php` | Create | `PaymentInit` → `HttpRedirect`; on return calls `PaymentStatus` |
| `src/Payum/Action/AuthorizeAction.php` | Create | `PaymentInit` URL generation (pre-auth semantics, no money movement yet) |
| `src/Payum/Action/RefundAction.php` | Create | `PaymentRefundRequest` |
| `src/Payum/Action/CancelAction.php` | Create | `PaymentReverseRequest` (pending) or `PaymentCloseRequest` (authorized) based on `paymentStatus` |
| `src/Payum/Action/StatusAction.php` | Create | `PaymentStatusRequest` → `GetHumanStatus` → Payum status constant |
| `src/Payum/Action/SyncAction.php` | Create | `PaymentStatusRequest` after notify callback |
| `src/Bundle/DependencyInjection/Compiler/PayumGatewayPass.php` | Create | CompilerPass: detects payum-bundle, registers gateway factory + action services |
| `tests/Tests/Payum/KHVPosGatewayFactoryTest.php` | Create | Standalone factory creation and gateway build |
| `tests/Tests/Payum/Action/CaptureActionTest.php` | Create | HttpRedirect thrown, PaymentStatus called on return |
| `tests/Tests/Payum/Action/AuthorizeActionTest.php` | Create | PaymentInit called, URL stored |
| `tests/Tests/Payum/Action/RefundActionTest.php` | Create | PaymentRefundRequest dispatched |
| `tests/Tests/Payum/Action/CancelActionTest.php` | Create | Correct cancel request chosen based on paymentStatus |
| `tests/Tests/Payum/Action/StatusActionTest.php` | Create | resultCode/paymentStatus → Payum status mapping |
| `tests/Tests/Payum/Action/SyncActionTest.php` | Create | PaymentStatus called, payment model updated |
| `tests/Tests/Bundle/PayumGatewayPassTest.php` | Create | CompilerPass registers correct service IDs and tags |

---

## GatewayFactory

`KHVPosGatewayFactory` implements `Payum\Core\GatewayFactoryInterface`.

`create(array $config, Gateway $gateway)` accepts:

| Key | Type | Default | Description |
|---|---|---|---|
| `merchant_id` | string | required | K&H merchant ID |
| `private_key_path` | string | required | Path to RSA private key PEM |
| `private_key_passphrase` | string | `''` | Optional passphrase |
| `mips_public_key_path` | string | `null` | MIPS public key path; `null` = use bundled key |
| `is_test` | bool | `false` | Use sandbox endpoint |
| `api_version` | string | `VPosClient::VERSION_REST_V1` | API version constant |

The factory builds an anonymous PSR container (same pattern as `VPosClientTest`), wires a `SignatureProvider` and full `Serializer` stack, and calls `setContainer()` on a new `VPosClient`. All six Actions are added to the `Gateway` via `addAction()`.

---

## Actions

### `ConvertPaymentAction`

Handles `Convert` with `Payment` as first model. Maps Payum's generic payment model to the `PaymentInitRequest`:

- `getNumber()` → `setOrderNumber()`
- `getTotalAmount()` (in minor units, Payum convention) → `setTotalAmount()`
- `getCurrencyCode()` → `setCurrency()` (via `Currency` enum)
- `getDetails()['return_url']` → `setReturnUrl()`
- `getDetails()['return_method']` → `setReturnMethod()` (default POST)

### `CaptureAction`

Handles `Capture` with `ArrayAccess` model.

**First pass (no `payId` stored yet):**
1. Builds `PaymentInitRequest` from model details
2. Calls `$client->send($request)` → receives `PaymentInitResponse`
3. Stores `payId` and `dttm` in model
4. Throws `HttpRedirect` with URL from `$client->getPaymentUrlWithPaymentProcessRequest()`

**Second pass (after bank redirect, `payId` present):**
1. Calls `PaymentStatusRequest` with stored `payId`
2. Stores full `PaymentStatusResponse` details in model
3. Returns — Payum proceeds to `StatusAction`

### `AuthorizeAction`

Handles `Authorize` with `ArrayAccess` model.

Same as CaptureAction first pass, but stores the redirect URL in `model['authorization_url']` instead of throwing `HttpRedirect`. Caller is responsible for presenting the URL. Useful for deferred payment flows.

### `StatusAction`

Handles `GetHumanStatus`. Reads `model['resultCode']` and `model['paymentStatus']`.

**Mapping:**

| Condition | Payum status |
|---|---|
| No `payId` stored | `new` |
| `resultCode = 0`, `paymentStatus` ∈ {1, 2} | `pending` |
| `resultCode = 150` | `pending` (3DS in progress) |
| `resultCode = 0`, `paymentStatus = 4` | `captured` |
| `resultCode = 0`, `paymentStatus` ∈ {6, 7, 8} | `canceled` |
| `resultCode = 0`, `paymentStatus` ∈ {3, 5} | `authorized` |
| `resultCode > 0` (other) | `failed` |

### `RefundAction`

Handles `Refund`. Reads `model['payId']` and `model['refund_amount']` (full amount if absent). Calls `PaymentRefundRequest`. Stores `resultCode` and `resultMessage` back into model.

### `CancelAction`

Handles `Cancel`. Reads `model['paymentStatus']`:
- Status `1` or `2` (pending, not yet authorized) → `PaymentReverseRequest`
- Status `3` or `5` (authorized, pre-capture) → `PaymentCloseRequest`
- Status `4` (already captured) → throws `\LogicException` (use `RefundAction` instead)

Stores result in model.

### `SyncAction`

Handles `Sync`. Calls `PaymentStatusRequest` with `model['payId']`. Updates model with fresh `paymentStatus`, `resultCode`, `resultMessage`. Used after IPN/notify callback.

---

## Bundle Auto-Wiring

### `PayumGatewayPass`

Implements `CompilerPassInterface`. Registered in `KHVPosBundle::build()`.

```php
public function process(ContainerBuilder $container): void
{
    if (!$container->hasDefinition('payum')) {
        return;
    }
    // Register KHVPosGatewayFactory as a Payum gateway factory
    // Tag: payum.gateway_factory, factory_name: khvpos, factory_alias: khvpos
    // Register each Action service with tag: payum.action, factory: khvpos
    // Wire khvpos.vpos_client as argument to all Action definitions
}
```

If `payum` service is not in the container (i.e. `payum/payum-bundle` is not installed), the pass is a no-op.

### Result in Symfony

With `payum/payum-bundle` installed, no additional YAML is needed. The `khvpos:` config block is sufficient:

```yaml
khvpos:
    test: false
    merchants:
        default:
            currency: HUF
            merchant_id: 'M123456789'
            private_key_path: '%kernel.project_dir%/config/keys/merchant.pem'
```

A `khvpos` Payum gateway is automatically available:

```php
$gateway = $payum->getGateway('khvpos');
$gateway->execute(new Capture($payment));
```

### `composer.json` changes

Add to `suggest`:
```json
"payum/core": "Payum payment processing integration (^2.0)"
```

Add to `require-dev`:
```json
"payum/payum-bundle": "^2.0"
```

Add to `autoload`:
```json
"KHTools\\VPos\\Payum\\": "src/Payum/"
```

---

## Tests

All tests are unit tests with mocked `VPosClient`.

**Action tests** (one file per Action): mock `VPosClient::send()` return value, assert:
- Correct request class passed to `send()`
- Correct fields set on request
- Correct Payum response thrown or returned
- Model updated with correct keys

**`KHVPosGatewayFactoryTest`**: call `create()` with minimal config, assert `Gateway` contains all six Action instances.

**`PayumGatewayPassTest`**: mock `ContainerBuilder` with a `payum` definition present; assert factory and action service definitions are registered with correct tags.

**CI:** No new workflow needed. `payum/payum-bundle` added to `require-dev` runs in the existing `tests.yml` matrix (PHP 8.4, 8.5).

---

## Out of Scope

- Payum storage integration (tokens, payments persistence) — Payum's generic storage layer handles this; no KH-specific code needed
- Notify/webhook endpoint — Payum's `NotifyAction` base handles routing; `SyncAction` handles the VPos status refresh
- One-click / Apple Pay / Google Pay Payum Actions — these use different Payum request types and can be a follow-up

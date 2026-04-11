# Bundle Merge — Design Spec

**Date:** 2026-04-11

---

## Goal

Merge `connorhu/khvpos-bundle` (`feature/v2` branch) into this repository under `src/Bundle/`, eliminating the need to maintain two separate packages. The Symfony integration becomes an optional part of `connorhu/khvpos` — Symfony dependencies are listed as `suggest`, so non-Symfony users are unaffected.

The `connorhu/khvpos-bundle` repository will be archived after the merge.

---

## Approach

Single-package merge with `suggest` dependencies (Option B). Bundle code lives at `src/Bundle/` with namespace `KHTools\VPos\Bundle\`. Symfony-specific packages (`symfony/http-kernel`, `symfony/config`, `symfony/dependency-injection`, `symfony/http-client`, `nyholm/psr7`) are declared in `suggest` — not `require`. PHP's lazy autoloading ensures bundle classes are never loaded unless the application explicitly uses them.

---

## File Structure

**New files (migrated from `feature/v2`, namespace updated):**

```
src/Bundle/
  KHVPosBundle.php
  DependencyInjection/
    Configuration.php
    PaymentGatewayExtension.php
  Providers/
    MerchantProvider.php
    MerchantProviderInterface.php
  Exceptions/
    ClientNotFoundException.php
    ConfigNotFoundException.php
  Resources/
    keys/
      mips_pay.khpos.hu.pub
      mips_pay.sandbox.khpos.hu.pub
```

**New test files:**

```
tests/Tests/Bundle/
  MerchantProviderTest.php
  ConfigurationTest.php
  PaymentGatewayExtensionTest.php
  Integration/
    TestKernel.php
    BundleIntegrationTest.php
```

**New CI workflow:**

```
.github/workflows/bundle.yml
```

**Dropped (stale artifact from earlier bundle design):**

`Resources/config/services.php` — referenced a non-existent `VPosClientProvider` class, was never loaded by `PaymentGatewayExtension`. Not migrated.

---

## Namespace Migration

| Old (`feature/v2`) | New |
|---|---|
| `KHTools\VPosBundle\` | `KHTools\VPos\Bundle\` |
| `KHTools\VPosBundle\DependencyInjection\` | `KHTools\VPos\Bundle\DependencyInjection\` |
| `KHTools\VPosBundle\Providers\` | `KHTools\VPos\Bundle\Providers\` |
| `KHTools\VPosBundle\Exceptions\` | `KHTools\VPos\Bundle\Exceptions\` |

---

## composer.json Changes

**Add to `autoload`:**
```json
"KHTools\\VPos\\Bundle\\": "src/Bundle/"
```

**Add `suggest` block:**
```json
"suggest": {
    "symfony/http-kernel": "Required for Symfony bundle integration (^6.4 || ^7.4)",
    "symfony/config": "Required for Symfony bundle integration (^6.4 || ^7.4)",
    "symfony/dependency-injection": "Required for Symfony bundle integration (^6.4 || ^7.4)",
    "symfony/http-client": "Required PSR-18 HTTP client implementation (^6.4 || ^7.4)",
    "nyholm/psr7": "Required PSR-7 implementation (^1.8)"
}
```

**Add to `require-dev`:**
```json
"nyholm/symfony-bundle-test": "^1.4",
"symfony/framework-bundle": "^6.4 || ^7.4"
```

`symfony/http-client` and `nyholm/psr7` remain in `require-dev` (already present for HTTP testing).

---

## Code Changes Required

**1. `VPosClient::VERSION_REST_V1` constant**

`PaymentGatewayExtension::Configuration` references `VPosClient::VERSION_REST_V1`. Verify this constant exists in the current library. If missing, add it to `VPosClient`:

```php
public const VERSION_REST_V1 = 'REST_V1';
```

**2. Namespace update throughout all migrated files**

Every `KHTools\VPosBundle\` reference becomes `KHTools\VPos\Bundle\`.

**3. `MerchantProvider::getMerchant()` — no change needed**

Uses `$merchant->merchantId` direct property access. `Merchant::$merchantId` is intentionally `public` (excluded from the Task 8 privatization), so this continues to work.

---

## DI Registration

`PaymentGatewayExtension::load()` registers:

| Service ID | Class | Notes |
|---|---|---|
| `khvpos.serializer.normalizer.*` | 6 normalizers from `src/Normalizers/` | Tagged `serializer.normalizer` at priority `-915` |
| `khvpos.signature_provider` | `SignatureProvider` | Lazy; private keys added via `addPrivateKey()` per merchant config |
| `khvpos.vpos_client` | `VPosClient` | Autowired, autoconfigured; aliased to `VPosClient::class` |
| `khvpos.merchant_provider` | `MerchantProvider` | Currency→MerchantId map; aliased to `MerchantProviderInterface::class` |

Symfony config key: `khvpos`. Example:

```yaml
khvpos:
    test: false
    mips_public_key_path: null        # null = use bundled K&H public key
    version: REST_V1
    merchants:
        default:
            currency: HUF
            merchant_id: 'M123456789'
            private_key_path: '%kernel.project_dir%/config/keys/merchant.pem'
            private_key_passphrase: ''
```

---

## Tests

**Unit tests** (`tests/Tests/Bundle/`):

- `MerchantProviderTest` — `getMerchant()` returns correct `Merchant` for known currency; throws `InvalidArgumentException` for unknown currency
- `ConfigurationTest` — config tree processes valid config; required fields enforced
- `PaymentGatewayExtensionTest` — services are registered with correct IDs and tags; aliases resolve correctly

**Integration tests** (`tests/Tests/Bundle/Integration/`):

- `TestKernel` — minimal kernel registering `FrameworkBundle` + `KHVPosBundle` with a test config
- `BundleIntegrationTest` — boots the kernel; asserts `VPosClient`, `SignatureProviderInterface`, `MerchantProviderInterface` are resolvable from the container; asserts all 6 serializer normalizers are tagged

Uses `nyholm/symfony-bundle-test` to avoid a full Symfony application setup.

---

## GitHub Actions CI

New workflow `.github/workflows/bundle.yml`:

```yaml
strategy:
  matrix:
    symfony: ['6.4', '7.4']
    php: ['8.4', '8.5']
```

4 combinations total. Each job:
1. Installs PHP at matrix version
2. Runs `composer require symfony/framework-bundle:^{symfony}.*` (flex-style version pin)
3. Runs `phpunit` scoped to `tests/Tests/Bundle/`

The existing `tests.yml` (library tests) is unchanged.

---

## Out of Scope

- Symfony Flex recipe (the bundle's `composer.json` had a custom recipes endpoint; this is not migrated — it was for the standalone package)
- Backward compatibility shim for `connorhu/khvpos-bundle` — the package is archived, v2 was never stable-released

<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum CustomerLoginAuth implements StringValueEnum
{
    case Guest;

    case Account;

    case Federated;

    case Issuer;

    case ThirdParty;

    case Fido;

    case SignedFido;

    case Api;

    public function stringValue(): string
    {
        return match ($this) {
            CustomerLoginAuth::Guest => 'guest',
            CustomerLoginAuth::Account => 'account',
            CustomerLoginAuth::Federated => 'federated',
            CustomerLoginAuth::Issuer => 'issuer',
            CustomerLoginAuth::ThirdParty => 'thirdparty',
            CustomerLoginAuth::Fido => 'fido',
            CustomerLoginAuth::SignedFido => 'fido_signed',
            CustomerLoginAuth::Api => 'api',
        };
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'guest' => CustomerLoginAuth::Guest,
            'account' => CustomerLoginAuth::Account,
            'federated' => CustomerLoginAuth::Federated,
            'issuer' => CustomerLoginAuth::Issuer,
            'thirdparty' => CustomerLoginAuth::ThirdParty,
            'fido' => CustomerLoginAuth::Fido,
            'fido_signed' => CustomerLoginAuth::SignedFido,
            'api' => CustomerLoginAuth::Api,
        };
    }
}

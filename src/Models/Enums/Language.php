<?php

namespace KHTools\VPos\Models\Enums;

enum Language implements StringValueEnum
{
    case HU;
    case EN;
    case DE;
    case FR;
    case CS;
    case IT;
    case JA;
    case PL;
    case PT;
    case RO;
    case RU;
    case SK;
    case ES;
    case TR;
    case VI;
    case HR;
    case SL;
    case SV;

    public function stringValue(): string
    {
        return match ($this) {
            self::HU => 'hu',
            self::EN => 'en',
            self::DE => 'de',
            self::FR => 'fr',
            self::CS => 'cs',
            self::IT => 'it',
            self::JA => 'ja',
            self::PL => 'pl',
            self::PT => 'pt',
            self::RO => 'ro',
            self::RU => 'ru',
            self::SK => 'sk',
            self::ES => 'es',
            self::TR => 'tr',
            self::VI => 'vi',
            self::HR => 'hr',
            self::SL => 'sl',
            self::SV => 'sv',
        };
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'hu' => self::HU,
            'en' => self::EN,
            'de' => self::DE,
            'fr' => self::FR,
            'cs' => self::CS,
            'it' => self::IT,
            'ja' => self::JA,
            'pl' => self::PL,
            'pt' => self::PT,
            'ro' => self::RO,
            'ru' => self::RU,
            'sk' => self::SK,
            'es' => self::ES,
            'tr' => self::TR,
            'vi' => self::VI,
            'hr' => self::HR,
            'sl' => self::SL,
            'sv' => self::SV,
        };
    }
}

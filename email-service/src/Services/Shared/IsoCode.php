<?php

declare(strict_types=1);

namespace App\Services\Shared;

enum IsoCode: string
{
    case PL_PL = 'pl_PL';
    case EN_GB = 'en_GB';
    case DE_DE = 'de_DE';
    case IT_IT = 'it_IT';
    case HR_HR = 'hr_HR';
    case ES_ES = 'es_ES';
    case FR_FR = 'fr_FR';
    case CS_CZ = 'cs_CZ';

    public static function fromLanguage(string $language): self
    {
        return match ($language) {
            'pl' => self::PL_PL,
            'en' => self::EN_GB,
            'de' => self::DE_DE,
            'it' => self::IT_IT,
            'hr' => self::HR_HR,
            'es' => self::ES_ES,
            'fr' => self::FR_FR,
            'cs' => self::CS_CZ,
            default => throw new \ValueError("Unsupported language: {$language}"),
        };
    }
}

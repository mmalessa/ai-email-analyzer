<?php

declare(strict_types=1);

namespace App\Services\LanguageDetector;

use App\Services\Shared\IsoCode;

interface LanguageDetector
{
    public function detect(string $text): IsoCode;
}

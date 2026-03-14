<?php

declare(strict_types=1);

namespace App\Service\LanguageDetector;

use App\Service\Shared\IsoCode;

interface LanguageDetector
{
    public function detect(string $text): IsoCode;
}

<?php

declare(strict_types=1);

namespace App\Service\ContextBuilder;

readonly class CandidateDevice
{
    public function __construct(
        public string $value,
    ) {}
}

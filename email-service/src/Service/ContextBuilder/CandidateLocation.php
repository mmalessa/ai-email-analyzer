<?php

declare(strict_types=1);

namespace App\Service\ContextBuilder;

readonly class CandidateLocation
{
    public function __construct(
        public string $value,
    ) {}
}

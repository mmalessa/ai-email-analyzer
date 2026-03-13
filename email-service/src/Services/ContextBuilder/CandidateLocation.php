<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder;

readonly class CandidateLocation
{
    public function __construct(
        public string $value,
    ) {}
}

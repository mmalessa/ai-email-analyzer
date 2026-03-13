<?php

declare(strict_types=1);

namespace App\Services\Shared;

readonly class IncidentId
{
    public function __construct(
        public string $value,
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}

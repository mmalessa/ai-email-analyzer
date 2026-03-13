<?php

declare(strict_types=1);

namespace App\Services\Shared;

readonly class Embedding
{
    /**
     * @param float[] $vector
     */
    public function __construct(
        public array $vector,
    ) {}

    public function dimensions(): int
    {
        return count($this->vector);
    }

    public function toArray(): array
    {
        return [
            'vector' => $this->vector,
            'dimensions' => $this->dimensions(),
        ];
    }
}

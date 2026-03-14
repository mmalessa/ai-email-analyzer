<?php

declare(strict_types=1);

namespace App\Service\Shared;

readonly class Emotions
{
    /**
     * @param string[] $emotions
     */
    public function __construct(
        public string $sentiment,
        public array $emotions,
        public int $intensity,
        public string $explanation,
    ) {}

    public function toArray(): array
    {
        return [
            'sentiment' => $this->sentiment,
            'emotions' => $this->emotions,
            'intensity' => $this->intensity,
            'explanation' => $this->explanation,
        ];
    }
}

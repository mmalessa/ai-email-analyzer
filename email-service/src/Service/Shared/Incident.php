<?php

declare(strict_types=1);

namespace App\Service\Shared;

readonly class Incident
{
    public function __construct(
        public string $deviceType,
        public ?string $deviceId,
        public ?string $location,
        public string $symptom,
        public string $priority,
    ) {}

    public function toArray(): array
    {
        return [
            'device_type' => $this->deviceType,
            'device_id' => $this->deviceId,
            'location' => $this->location,
            'symptom' => $this->symptom,
            'priority' => $this->priority,
        ];
    }
}

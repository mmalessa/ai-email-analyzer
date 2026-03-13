<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\SimilarIncident;
use App\Services\Shared\Embedding;

interface SimilarIncidentRepository
{
    /** @return SimilarIncident[] */
    public function get(
        Embedding $embedding,
        ?string $language = null,
        ?string $emailFrom = null,
        ?string $emailDomain = null,
    ): array;

    public function add(
        Embedding $embedding,
        string $incidentId,
        string $text,
        ?string $device = null,
        ?string $location = null,
        ?string $symptom = null,
        ?string $language = null,
        ?string $emailFrom = null,
        ?string $emailDomain = null,
        ?\DateTimeImmutable $createdAt = null,
    ): void;
}

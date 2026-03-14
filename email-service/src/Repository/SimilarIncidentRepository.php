<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\ContextBuilder\SimilarIncident;
use App\Service\Shared\Embedding;

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

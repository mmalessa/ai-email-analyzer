<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder;

use App\Services\Shared\Embedding;

readonly class IncidentContextBundle
{
    /**
     * @param CandidateLocation[] $candidateLocations
     * @param CandidateDevice[] $candidateDevices
     * @param SimilarIncident[] $similarIncidents
     */
    public function __construct(
        public string $language,
        public array $candidateLocations = [],
        public array $candidateDevices = [],
        public array $similarIncidents = [],
        public Embedding $embedding = new Embedding([]),
    ) {}
}

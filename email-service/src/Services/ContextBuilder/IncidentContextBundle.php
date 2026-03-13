<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder;

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
    ) {}
}

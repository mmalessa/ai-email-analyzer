<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder;

use App\Services\ContextBuilder\Repository\CandidateDeviceRepository;
use App\Services\ContextBuilder\Repository\CandidateLocationRepository;
use App\Services\ContextBuilder\Repository\SimilarIncidentRepository;
use App\Services\Shared\IsoCode;

class IncidentContextBuilder
{
    public function __construct(
        private CandidateLocationRepository $candidateLocationRepository,
        private CandidateDeviceRepository $candidateDeviceRepository,
        private SimilarIncidentRepository $similarIncidentRepository,
    ) {}

    public function build(IsoCode $isoCode): IncidentContextBundle
    {
        return new IncidentContextBundle(
            language: $isoCode->value,
            candidateLocations: $this->candidateLocationRepository->get($isoCode),
            candidateDevices: $this->candidateDeviceRepository->get($isoCode),
            similarIncidents: $this->similarIncidentRepository->get($isoCode),
        );
    }
}

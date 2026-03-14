<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder;

use App\Services\ContextBuilder\Repository\CandidateDeviceRepository;
use App\Services\ContextBuilder\Repository\CandidateLocationRepository;
use App\Services\ContextBuilder\Repository\SimilarIncidentRepository;
use App\Services\EmbeddingService\EmbeddingService;
use App\Services\Shared\EmailData;
use App\Services\Shared\IsoCode;

class IncidentContextBuilder
{
    public function __construct(
        private CandidateLocationRepository $candidateLocationRepository,
        private CandidateDeviceRepository $candidateDeviceRepository,
        private SimilarIncidentRepository $similarIncidentRepository,
        private EmbeddingService $embeddingService,
    ) {}

    public function build(IsoCode $isoCode, EmailData $emailData): IncidentContextBundle
    {
        $embedding = $this->embeddingService->embed($emailData->body);

        return new IncidentContextBundle(
            language: $isoCode->value,
            candidateLocations: $this->candidateLocationRepository->get($isoCode),
            candidateDevices: $this->candidateDeviceRepository->get($isoCode),
            similarIncidents: $this->similarIncidentRepository->get(
                embedding:  $embedding,
//                language: $isoCode->value,
//                emailFrom: $emailData->from,
                emailDomain: $emailData->fromDomain
            ),
            embedding: $embedding,
        );
    }
}

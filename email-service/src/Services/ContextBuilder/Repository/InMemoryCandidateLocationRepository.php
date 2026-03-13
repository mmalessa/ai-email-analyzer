<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\CandidateLocation;
use App\Services\Shared\IsoCode;

class InMemoryCandidateLocationRepository implements CandidateLocationRepository
{
    /** @return CandidateLocation[] */
    public function get(IsoCode $isoCode): array
    {
        return match ($isoCode) {
            IsoCode::PL_PL => [
                new CandidateLocation('Kraków Główny'),
                new CandidateLocation('Warszawa Centralna'),
                new CandidateLocation('Gdańsk Wrzeszcz'),
            ],
            IsoCode::DE_DE => [
                new CandidateLocation('Berlin Hauptbahnhof'),
                new CandidateLocation('München Hbf'),
            ],
            IsoCode::CS_CZ => [
                new CandidateLocation('Praha hlavní nádraží'),
                new CandidateLocation('Brno hlavní nádraží'),
            ],
            default => [],
        };
    }
}

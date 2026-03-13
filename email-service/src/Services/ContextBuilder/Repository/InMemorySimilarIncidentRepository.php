<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\SimilarIncident;
use App\Services\Shared\IsoCode;

class InMemorySimilarIncidentRepository implements SimilarIncidentRepository
{
    /** @return SimilarIncident[] */
    public function get(IsoCode $isoCode): array
    {
        return match ($isoCode) {
            IsoCode::PL_PL => [
                new SimilarIncident('INC-2026-0042: Awaria silnika EN57 na stacji Kraków'),
                new SimilarIncident('INC-2026-0038: Dym z przedziału silnikowego EP09'),
            ],
            IsoCode::DE_DE => [
                new SimilarIncident('INC-2026-0051: Triebwerkstörung ICE Berlin-München'),
            ],
            IsoCode::CS_CZ => [
                new SimilarIncident('INC-2026-0063: Porucha motoru ČD 380 Praha'),
            ],
            default => [],
        };
    }
}

<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\SimilarIncident;
use App\Services\Shared\IsoCode;

interface SimilarIncidentRepository
{
    /** @return SimilarIncident[] */
    public function get(IsoCode $isoCode): array;
}

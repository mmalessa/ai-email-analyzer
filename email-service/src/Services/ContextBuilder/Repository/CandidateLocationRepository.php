<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\CandidateLocation;
use App\Services\Shared\IsoCode;

interface CandidateLocationRepository
{
    /** @return CandidateLocation[] */
    public function get(IsoCode $isoCode): array;
}

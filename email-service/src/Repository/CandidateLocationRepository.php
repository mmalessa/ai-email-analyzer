<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\ContextBuilder\CandidateLocation;
use App\Service\Shared\IsoCode;

interface CandidateLocationRepository
{
    /** @return CandidateLocation[] */
    public function get(IsoCode $isoCode): array;
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\ContextBuilder\CandidateDevice;
use App\Service\Shared\IsoCode;

interface CandidateDeviceRepository
{
    /** @return CandidateDevice[] */
    public function get(IsoCode $isoCode): array;
}

<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\CandidateDevice;
use App\Services\Shared\IsoCode;

interface CandidateDeviceRepository
{
    /** @return CandidateDevice[] */
    public function get(IsoCode $isoCode): array;
}

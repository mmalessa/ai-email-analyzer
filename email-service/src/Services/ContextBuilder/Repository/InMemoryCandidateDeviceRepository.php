<?php

declare(strict_types=1);

namespace App\Services\ContextBuilder\Repository;

use App\Services\ContextBuilder\CandidateDevice;
use App\Services\Shared\IsoCode;

class InMemoryCandidateDeviceRepository implements CandidateDeviceRepository
{
    /** @return CandidateDevice[] */
    public function get(IsoCode $isoCode): array
    {
        return match ($isoCode) {
            IsoCode::PL_PL => [
                new CandidateDevice('EN57-1234'),
                new CandidateDevice('ED74-001'),
                new CandidateDevice('EP09-035'),
            ],
            IsoCode::DE_DE => [
                new CandidateDevice('ICE-4810'),
                new CandidateDevice('BR101-120'),
            ],
            IsoCode::CS_CZ => [
                new CandidateDevice('ČD 380-001'),
                new CandidateDevice('ČD 471-055'),
            ],
            default => [],
        };
    }
}

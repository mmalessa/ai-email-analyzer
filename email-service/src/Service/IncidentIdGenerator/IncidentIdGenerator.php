<?php

declare(strict_types=1);

namespace App\Service\IncidentIdGenerator;

use App\Service\Shared\IncidentId;
use Doctrine\DBAL\Connection;

class IncidentIdGenerator
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function generate(): IncidentId
    {
        $today = date('Ymd');

        $max = $this->connection->fetchOne(
            "SELECT MAX(CAST(RIGHT(incident_id, 6) AS INTEGER)) FROM incidents WHERE incident_id LIKE :prefix",
            ['prefix' => 'INC-' . $today . '-%'],
        );

        $sequence = ($max === null || $max === false) ? 1 : (int) $max + 1;

        return new IncidentId(sprintf('INC-%s-%06d', $today, $sequence));
    }
}

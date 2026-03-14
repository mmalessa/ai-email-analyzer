<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\ContextBuilder\SimilarIncident;
use App\Service\Shared\Embedding;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class PostgresSimilarIncidentRepository implements SimilarIncidentRepository
{
    public function __construct(
        private Connection $connection,
        private LoggerInterface $logger,
        private int $similarIncidentLimit,
    ) {}

    /** @return SimilarIncident[] */
    public function get(
        Embedding $embedding,
        ?string $language = null,
        ?string $emailFrom = null,
        ?string $emailDomain = null,
    ): array {
        $vectorLiteral = '[' . implode(',', $embedding->vector) . ']';

        $conditions = [];
        $params = [];

        if ($language !== null) {
            $conditions[] = 'language = :language';
            $params['language'] = $language;
        }
        if ($emailFrom !== null) {
            $conditions[] = 'email_from = :email_from';
            $params['email_from'] = $emailFrom;
        }
        if ($emailDomain !== null) {
            $conditions[] = 'email_domain = :email_domain';
            $params['email_domain'] = $emailDomain;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT incident_id, text, device, location, symptom
                FROM incidents
                {$where}
                ORDER BY embedding <=> '{$vectorLiteral}'::vector
                LIMIT {$this->similarIncidentLimit}";

        try {
            $rows = $this->connection->executeQuery($sql, $params)->fetchAllAssociative();
        } catch (\Throwable $e) {
            $this->logger->error('PostgreSQL similarity query failed', ['error' => $e->getMessage()]);
            return [];
        }

        return array_map(
            fn(array $row) => new SimilarIncident($this->formatIncident($row)),
            $rows,
        );
    }

    public function add(
        Embedding $embedding,
        string $incidentId,
        string $text,
        ?string $device = null,
        ?string $location = null,
        ?string $symptom = null,
        ?string $language = null,
        ?string $emailFrom = null,
        ?string $emailDomain = null,
        ?\DateTimeImmutable $createdAt = null,
    ): void {
        $vectorLiteral = '[' . implode(',', $embedding->vector) . ']';

        try {
            $this->connection->executeStatement(
                "INSERT INTO incidents
                    (incident_id, text, device, location, symptom, language, email_from, email_domain, created_at, embedding)
                 VALUES
                    (:incident_id, :text, :device, :location, :symptom, :language, :email_from, :email_domain, :created_at, '{$vectorLiteral}'::vector)",
                [
                    'incident_id' => $incidentId,
                    'text'        => $text,
                    'device'      => $device,
                    'location'    => $location,
                    'symptom'     => $symptom,
                    'language'    => $language,
                    'email_from'  => $emailFrom,
                    'email_domain' => $emailDomain,
                    'created_at'  => $createdAt?->format('Y-m-d H:i:sP'),
                ],
            );
        } catch (\Throwable $e) {
            $this->logger->error('PostgreSQL insert failed', [
                'incident_id' => $incidentId,
                'error'       => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function formatIncident(array $item): string
    {
        $parts = [];

        if (!empty($item['incident_id'])) {
            $parts[] = $item['incident_id'] . ':';
        }

        if (!empty($item['text'])) {
            $parts[] = $item['text'];
        } else {
            $fields = array_filter([
                $item['device'] ?? null,
                $item['location'] ?? null,
                $item['symptom'] ?? null,
            ]);
            $parts[] = implode(', ', $fields);
        }

        return implode(' ', $parts);
    }
}

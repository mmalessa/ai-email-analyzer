<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\ContextBuilder\SimilarIncident;
use App\Service\Shared\Embedding;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeaviateSimilarIncidentRepository implements SimilarIncidentRepository
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl,
        private int $similarIncidentLimit,
    ) {}

    /** @return SimilarIncident[] */
    public function get(
        Embedding $embedding,
        ?string $language = null,
        ?string $emailFrom = null,
        ?string $emailDomain = null,
    ): array {
        $nearVector = sprintf('nearVector: { vector: %s }', json_encode($embedding->vector));
        $where = $this->buildWhereClause($language, $emailFrom, $emailDomain);
        $limit = sprintf('limit: %d', $this->similarIncidentLimit);

        $args = implode(', ', array_filter([$nearVector, $where, $limit]));

        $query = sprintf(
            '{ Get { Incident(%s) { incident_id text device location symptom _additional { distance } } } }',
            $args,
        );

        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/v1/graphql', [
                'json' => ['query' => $query],
                'timeout' => 10,
            ]);

            $data = $response->toArray();
        } catch (\Throwable $e) {
            $this->logger->error('Weaviate query failed', ['error' => $e->getMessage()]);
            return [];
        }

        $incidents = $data['data']['Get']['Incident'] ?? [];

        return array_map(
            fn(array $item) => new SimilarIncident($this->formatIncident($item)),
            $incidents,
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
        $properties = [
            'incident_id' => $incidentId,
            'text' => $text,
        ];

        if ($device !== null) {
            $properties['device'] = $device;
        }
        if ($location !== null) {
            $properties['location'] = $location;
        }
        if ($symptom !== null) {
            $properties['symptom'] = $symptom;
        }
        if ($language !== null) {
            $properties['language'] = $language;
        }
        if ($emailFrom !== null) {
            $properties['email_from'] = $emailFrom;
        }
        if ($emailDomain !== null) {
            $properties['email_domain'] = $emailDomain;
        }
        if ($createdAt !== null) {
            $properties['created_at'] = $createdAt->format('c');
        }

        $payload = [
            'class' => 'Incident',
            'properties' => $properties,
            'vector' => $embedding->vector,
        ];

        try {
            $response = $this->httpClient->request('POST', $this->baseUrl . '/v1/objects', [
                'json' => $payload,
                'timeout' => 10,
            ]);

            $response->toArray();
        } catch (\Throwable $e) {
            $this->logger->error('Weaviate insert failed', [
                'incident_id' => $incidentId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function buildWhereClause(?string $language, ?string $emailFrom, ?string $emailDomain): ?string
    {
        $operands = [];

        if ($language !== null) {
            $operands[] = sprintf(
                '{ path: ["language"], operator: Equal, valueText: "%s" }',
                addslashes($language),
            );
        }

        if ($emailFrom !== null) {
            $operands[] = sprintf(
                '{ path: ["email_from"], operator: Equal, valueText: "%s" }',
                addslashes($emailFrom),
            );
        }

        if ($emailDomain !== null) {
            $operands[] = sprintf(
                '{ path: ["email_domain"], operator: Equal, valueText: "%s" }',
                addslashes($emailDomain),
            );
        }

        if (empty($operands)) {
            return null;
        }

        if (count($operands) === 1) {
            return sprintf('where: %s', $operands[0]);
        }

        return sprintf(
            'where: { operator: And, operands: [%s] }',
            implode(', ', $operands),
        );
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

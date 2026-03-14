<?php

declare(strict_types=1);

namespace App\Service\EmbeddingService;

use App\Service\Shared\Embedding;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaEmbeddingService implements EmbeddingService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl,
        private string $model,
    ) {}

    public function embed(string $text): Embedding
    {
        $url = $this->baseUrl . '/api/embeddings';

        try {
            $response = $this->httpClient->request(
                'POST',
                $url,
                [
                    'json' => [
                        'model' => $this->model,
                        'prompt' => $text,
                    ],
                    'timeout' => 30,
                ]
            );

            $data = $response->toArray();
        } catch (\Throwable $e) {
            $this->logger->error('Embedding request failed', ['url' => $url, 'error' => $e->getMessage()]);
            throw new \RuntimeException('Embedding service unavailable: ' . $e->getMessage(), 0, $e);
        }

        $vector = $data['embedding'] ?? [];

        $this->logger->info('Embedding generated', ['dimensions' => count($vector)]);

        return new Embedding($vector);
    }
}

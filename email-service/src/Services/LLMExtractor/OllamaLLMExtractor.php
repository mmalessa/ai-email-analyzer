<?php

declare(strict_types=1);

namespace App\Services\LLMExtractor;

use App\Services\Shared\Emotions;
use App\Services\Shared\Incident;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OllamaLLMExtractor implements LLMExtractor
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl,
        private string $model,
        private float $temperatureIncident,
        private float $temperatureEmotions,
    ) {}

    /** @return Incident[] */
    public function extractIncident(string $systemPrompt, string $emailBody): array
    {
        $parsed = $this->chat($systemPrompt, $emailBody, $this->temperatureIncident);
        $incidents = $parsed['incidents'] ?? [];

        return array_map(
            fn(array $item) => new Incident(
                deviceType: $item['device_type'] ?? '',
                deviceId: $item['device_id'] ?? null,
                location: $item['location'] ?? null,
                symptom: $item['symptom'] ?? '',
                priority: $item['priority'] ?? '',
            ),
            $incidents,
        );
    }

    public function extractEmotions(string $systemPrompt, string $text): Emotions
    {
        $parsed = $this->chat($systemPrompt, $text, $this->temperatureEmotions);

        return new Emotions(
            sentiment: $parsed['sentiment'] ?? 'neutral',
            emotions: $parsed['emotions'] ?? [],
            intensity: (int) ($parsed['intensity'] ?? 0),
            explanation: $parsed['explanation'] ?? '',
        );
    }

    private function chat(string $systemPrompt, string $userMessage, float $temperature): array
    {
        $url = $this->baseUrl . '/v1/chat/completions';

        try {
            $response = $this->httpClient->request(
                'POST',
                $url,
                [
                    'json' => [
                        'model' => $this->model,
                        'temperature' => $temperature,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $systemPrompt,
                            ],
                            [
                                'role' => 'user',
                                'content' => $userMessage,
                            ],
                        ],
                    ],
                    'timeout' => 120,
                ]
            );

            $data = $response->toArray();
        } catch (\Throwable $e) {
            $this->logger->error('Ollama request failed', ['url' => $url, 'error' => $e->getMessage()]);
            throw new \RuntimeException('LLM service unavailable: ' . $e->getMessage(), 0, $e);
        }

        $content = $data['choices'][0]['message']['content'] ?? '';
        $this->logger->debug('Ollama raw response', ['content' => $content]);

        try {
            return json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->logger->error('Ollama returned invalid JSON', ['content' => $content, 'error' => $e->getMessage()]);
            throw new \RuntimeException('LLM returned invalid JSON: ' . $e->getMessage(), 0, $e);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Service\LanguageDetector;

use App\Service\Shared\IsoCode;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LanguageDetectorFastText implements LanguageDetector
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $baseUrl,
        private float $confidenceThreshold,
    ) {}

    public function detect(string $text): IsoCode
    {
        try {
            $response = $this->httpClient->request(
                'POST',
                $this->baseUrl . '/detect',
                [
                    'json' => ['text' => $text],
                    'timeout' => 5,
                ]
            );

            $data = $response->toArray();
        } catch (\Throwable $e) {
            $this->logger->error('Language detection failed', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Language detection service unavailable: ' . $e->getMessage(), 0, $e);
        }

        $confidence = $data['confidence'] ?? 0.0;
        $language = $data['language'] ?? '';

        $this->logger->info('Language detection result', ['language' => $language, 'confidence' => $confidence]);

        if ($confidence < $this->confidenceThreshold) {
            throw new \RuntimeException(sprintf(
                'Language detection confidence too low: %.2f (threshold: %.2f)',
                $confidence,
                $this->confidenceThreshold,
            ));
        }

        return IsoCode::fromLanguage($language);
    }
}

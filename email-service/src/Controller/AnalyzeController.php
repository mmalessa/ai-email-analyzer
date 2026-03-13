<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\ContextBuilder\IncidentContextBuilder;
use App\Services\ContextPromptBuilder\EmotionsContextPromptBuilder;
use App\Services\ContextPromptBuilder\IncidentContextPromptBuilder;
use App\Services\LanguageDetector\LanguageDetector;
use App\Services\LLMExtractor\LLMExtractor;
use App\Services\Shared\EmailData;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AnalyzeController
{
    public function __construct(
        private LanguageDetector $languageDetector,
        private IncidentContextBuilder $incidentContextBuilder,
        private IncidentContextPromptBuilder $incidentContextPromptBuilder,
        private EmotionsContextPromptBuilder $emotionsContextPromptBuilder,
        private LLMExtractor $llmExtractor,
        private LoggerInterface $logger,
    ) {}

    #[Route('/analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $emailData = EmailData::fromArray($data);

        $this->logger->info('Analyzing email', ['from' => $emailData->from, 'subject' => $emailData->subject]);

        $isoCode = $this->languageDetector->detect($emailData->body);
        $this->logger->info('Language detected', ['language' => $isoCode->value]);

        $incidentContextBundle = $this->incidentContextBuilder->build($isoCode);
        $this->logger->info('Context built', [
            'locations' => count($incidentContextBundle->candidateLocations),
            'devices' => count($incidentContextBundle->candidateDevices),
            'similar_incidents' => count($incidentContextBundle->similarIncidents),
        ]);

        $incidentSystemPrompt = $this->incidentContextPromptBuilder->buildSystemPrompt($incidentContextBundle);
        $incidents = $this->llmExtractor->extractIncident($incidentSystemPrompt, $emailData->body);
        $this->logger->info('Incidents extracted', ['count' => count($incidents)]);

        $emotionsSystemPrompt = $this->emotionsContextPromptBuilder->buildSystemPrompt();
        $emotions = $this->llmExtractor->extractEmotions($emotionsSystemPrompt, $emailData->body);
        $this->logger->info('Emotions extracted', ['sentiment' => $emotions->sentiment, 'intensity' => $emotions->intensity]);

        return new JsonResponse([
            ...$emailData->toArray(),
            'language' => $isoCode->value,
            'incidents' => array_map(fn($i) => $i->toArray(), $incidents),
            'emotions' => $emotions->toArray(),
        ]);
    }
}

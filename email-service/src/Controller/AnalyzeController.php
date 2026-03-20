<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ContextBuilder\IncidentContextBuilder;
use App\Repository\SimilarIncidentRepository;
use App\Service\ContextPromptBuilder\EmotionsContextPromptBuilder;
use App\Service\ContextPromptBuilder\IncidentContextPromptBuilder;
use App\Service\IncidentIdGenerator\IncidentIdGenerator;
use App\Service\LanguageDetector\LanguageDetector;
use App\Service\LLMExtractor\LLMExtractor;
use App\Service\Shared\EmailData;
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
        private IncidentIdGenerator $incidentIdGenerator,
        private SimilarIncidentRepository $similarIncidentRepository,
        private LoggerInterface $logger,
        private string $ollamaModelIncident,
        private string $ollamaModelEmotions,
        private float $temperatureIncident,
        private float $temperatureEmotions,
    ) {}

    #[Route('/analyze', methods: ['POST'])]
    public function analyze(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        $emailData = EmailData::fromArray($data);

        $incidentId = $this->incidentIdGenerator->generate();
        $this->logger->info('Analyzing email', ['incident_id' => $incidentId->value, 'from' => $emailData->from, 'subject' => $emailData->subject]);

        $isoCode = $this->languageDetector->detect($emailData->body);
        $this->logger->info('Language detected', ['language' => $isoCode->value]);

        $incidentContextBundle = $this->incidentContextBuilder->build($isoCode, $emailData);
        $this->logger->info('Context built', [
            'locations' => count($incidentContextBundle->candidateLocations),
            'devices' => count($incidentContextBundle->candidateDevices),
            'similar_incidents' => count($incidentContextBundle->similarIncidents),
        ]);

        $incidentSystemPrompt = $this->incidentContextPromptBuilder->buildSystemPrompt($incidentContextBundle);
        $incidents = $this->llmExtractor->extractIncident($incidentSystemPrompt, $emailData->body, $this->ollamaModelIncident, $this->temperatureIncident);
        $this->logger->info('Incidents extracted', ['count' => count($incidents)]);

        $emotionsSystemPrompt = $this->emotionsContextPromptBuilder->buildSystemPrompt();
        $emotions = $this->llmExtractor->extractEmotions($emotionsSystemPrompt, $emailData->body, $this->ollamaModelEmotions, $this->temperatureEmotions);
        $this->logger->info('Emotions extracted', ['sentiment' => $emotions->sentiment, 'intensity' => $emotions->intensity]);

        $firstIncident = $incidents[0] ?? null;
        $this->similarIncidentRepository->add(
            embedding: $incidentContextBundle->embedding,
            incidentId: $incidentId->value,
            text: $emailData->body,
            device: $firstIncident?->deviceType,
            location: $firstIncident?->location,
            symptom: $firstIncident?->symptom,
            language: $isoCode->value,
            emailFrom: $emailData->from,
            emailDomain: $emailData->fromDomain,
            createdAt: new \DateTimeImmutable($emailData->receivedAt),
        );
        $this->logger->info('Incident stored in Weaviate', ['incident_id' => $incidentId->value]);

        return new JsonResponse([
            'incident_id' => $incidentId->value,
            ...$emailData->toArray(),
            'language' => $isoCode->value,
            'incidents' => array_map(fn($i) => $i->toArray(), $incidents),
            'emotions' => $emotions->toArray(),
        ]);
    }
}

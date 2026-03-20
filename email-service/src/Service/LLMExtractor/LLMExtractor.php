<?php

declare(strict_types=1);

namespace App\Service\LLMExtractor;

use App\Service\Shared\Emotions;
use App\Service\Shared\Incident;

interface LLMExtractor
{
    /** @return Incident[] */
    public function extractIncident(string $systemPrompt, string $emailBody, string $model, float $temperature): array;

    public function extractEmotions(string $systemPrompt, string $text, string $model, float $temperature): Emotions;
}

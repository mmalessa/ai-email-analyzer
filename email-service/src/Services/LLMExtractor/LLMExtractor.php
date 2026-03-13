<?php

declare(strict_types=1);

namespace App\Services\LLMExtractor;

use App\Services\Shared\Emotions;
use App\Services\Shared\Incident;

interface LLMExtractor
{
    /** @return Incident[] */
    public function extractIncident(string $systemPrompt, string $emailBody): array;

    public function extractEmotions(string $systemPrompt, string $text): Emotions;
}

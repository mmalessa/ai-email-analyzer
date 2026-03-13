<?php

declare(strict_types=1);

namespace App\Services\ContextPromptBuilder;

class EmotionsContextPromptBuilder
{
    public function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
            You analyze emotional tone of text.
            Return ONLY valid JSON, no markdown, no extra text.

            JSON schema:
            {
              "sentiment": "positive | neutral | negative",
              "emotions": ["list of detected emotions, e.g. frustration, anger, sadness, joy"],
              "intensity": "0-100, how strong the emotional tone is",
              "explanation": "brief explanation of the emotional analysis"
            }
            PROMPT;
    }
}

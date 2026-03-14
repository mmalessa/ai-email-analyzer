<?php

declare(strict_types=1);

namespace App\Service\ContextPromptBuilder;

use App\Service\ContextBuilder\IncidentContextBundle;

class IncidentContextPromptBuilder
{
    public function buildSystemPrompt(IncidentContextBundle $bundle): string
    {
        $locations = $this->formatList($bundle->candidateLocations);
        $devices = $this->formatList($bundle->candidateDevices);
        $incidents = $this->formatIncidents($bundle->similarIncidents);

        return <<<PROMPT
            You are an AI system analyzing maintenance emails.
            Return ONLY valid JSON, no markdown, no extra text.

            Language: {$bundle->language}

            KNOWN LOCATIONS
            $locations

            KNOWN DEVICE TYPES
            $devices

            SIMILAR INCIDENTS
            $incidents

            Rules:

            - An email may contain multiple incidents.
            - Use the provided device types and locations when possible.
            - If device id appears (example: freezer 2), extract it.
            - If location is mentioned, match it with known locations.

            JSON schema:

            {
              "incidents":[
                {
                  "device_type": "",
                  "device_id": "",
                  "location": "",
                  "symptom": "",
                  "priority": ""
                }
              ]
            }

            Priority rules:

            device stopped -> critical
            temperature rise -> high
            maintenance -> low
            PROMPT;
    }

    private function formatList(array $items): string
    {
        if (empty($items)) {
            return "- none";
        }

        return implode("\n", array_map(
            fn($item) => "- {$item->value}",
            $items
        ));
    }

    private function formatIncidents(array $incidents): string
    {
        if (empty($incidents)) {
            return "- none";
        }

        return implode("\n", array_map(
            fn($incident) => "- {$incident->value}",
            $incidents
        ));
    }
}

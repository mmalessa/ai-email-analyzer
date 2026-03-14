<?php

declare(strict_types=1);

namespace App\Service\EmbeddingService;

use App\Service\Shared\Embedding;

interface EmbeddingService
{
    public function embed(string $text): Embedding;
}

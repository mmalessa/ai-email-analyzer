<?php

declare(strict_types=1);

namespace App\Services\EmbeddingService;

use App\Services\Shared\Embedding;

interface EmbeddingService
{
    public function embed(string $text): Embedding;
}

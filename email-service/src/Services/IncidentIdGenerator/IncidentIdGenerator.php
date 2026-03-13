<?php

declare(strict_types=1);

namespace App\Services\IncidentIdGenerator;

use App\Services\Shared\IncidentId;
use Psr\Cache\CacheItemPoolInterface;

class IncidentIdGenerator
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {}

    public function generate(): IncidentId
    {
        $today = date('Ymd');
        $cacheKey = 'incident_seq_' . $today;

        $item = $this->cache->getItem($cacheKey);

        $sequence = $item->isHit() ? $item->get() + 1 : 1;

        $item->set($sequence);
        $item->expiresAfter(86400);
        $this->cache->save($item);

        return new IncidentId(sprintf('INC-%s-%06d', $today, $sequence));
    }
}

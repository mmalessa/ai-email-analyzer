<?php

declare(strict_types=1);

namespace App\Services\Shared;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class PostgresConnectionFactory
{
    public static function fromUrl(string $url): Connection
    {
        $p = parse_url($url);

        return DriverManager::getConnection([
            'driver'   => 'pdo_pgsql',
            'host'     => $p['host'],
            'port'     => $p['port'] ?? 5432,
            'dbname'   => ltrim($p['path'] ?? '', '/'),
            'user'     => $p['user'] ?? '',
            'password' => $p['pass'] ?? '',
        ]);
    }
}

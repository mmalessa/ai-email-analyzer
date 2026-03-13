<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener]
class ExceptionListener
{
    public function __construct(
        private string $appEnv = 'prod',
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $status = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $body = [
            'error' => $status >= 500 && $this->appEnv === 'prod'
                ? 'Internal server error'
                : $exception->getMessage(),
        ];

        if ($this->appEnv === 'dev') {
            $body['exception'] = $exception::class;
            $body['trace'] = array_slice(
                array_map(fn(array $frame) => sprintf(
                    '%s:%d',
                    $frame['file'] ?? 'unknown',
                    $frame['line'] ?? 0,
                ), $exception->getTrace()),
                0,
                10,
            );
        }

        $event->setResponse(new JsonResponse($body, $status));
    }
}

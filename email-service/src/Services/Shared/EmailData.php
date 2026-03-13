<?php

declare(strict_types=1);

namespace App\Services\Shared;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EmailData
{
    public string $fromDomain {
        get => substr($this->from, strrpos($this->from, '@') + 1);
    }

    public function __construct(
        public readonly string $receivedAt,
        public readonly string $from,
        public readonly string $to,
        public readonly string $subject,
        public readonly string $body,
    ) {}

    public static function fromArray(array $data): self
    {
        $required = ['received_at', 'from', 'to', 'subject', 'body'];
        $missing = array_diff($required, array_keys($data));

        if ($missing) {
            throw new BadRequestHttpException('Missing required fields: ' . implode(', ', $missing));
        }

        if (trim($data['body']) === '') {
            throw new BadRequestHttpException('Field "body" cannot be empty');
        }

        return new self(
            receivedAt: $data['received_at'],
            from: $data['from'],
            to: $data['to'],
            subject: $data['subject'],
            body: $data['body'],
        );
    }

    public function toArray(): array
    {
        return [
            'received_at' => $this->receivedAt,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
        ];
    }
}

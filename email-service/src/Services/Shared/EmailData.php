<?php

declare(strict_types=1);

namespace App\Services\Shared;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class EmailData
{
    public function __construct(
        public string $receivedAt,
        public string $from,
        public string $to,
        public string $subject,
        public string $body,
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

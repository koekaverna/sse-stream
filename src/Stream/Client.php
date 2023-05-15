<?php

declare(strict_types=1);

namespace App\Stream;

use Ramsey\Uuid\Uuid;
use React\Stream\WritableStreamInterface;

final class Client
{
    private readonly string $id;
    public function __construct(
        private readonly WritableStreamInterface $stream,
        string $id = null,
    ) {
        if ($id === null) {
            $this->id = Uuid::uuid4()->toString();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStream(): WritableStreamInterface
    {
        return $this->stream;
    }
}

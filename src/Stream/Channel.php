<?php

declare(strict_types=1);

namespace App\Stream;

final class Channel
{
    /** @var array<Client> */
    private array $clients = [];

    private int $messageCount = 0;

    public function addClient(Client $client): void
    {
        $this->clients[$client->getId()] = $client;

        $stream = $client->getStream();
        $stream->on('close', fn() => $this->removeClient($client));
        dump('Client connected.');
    }

    public function removeClient(Client $client): void
    {
        unset($this->clients[$client->getId()]);
        dump('Client disconnected.');
    }

    public function sendServerSentEvent(string $event, array $data): void
    {
        $this->messageCount++;
        foreach ($this->clients as $client) {
            $client->getStream()->write(
                "id: {$this->messageCount}\n" .
                "event: {$event}\n" .
                "data: " . json_encode($data) . "\n\n"
            );
        }
    }
}

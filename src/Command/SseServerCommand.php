<?php

declare(strict_types=1);

namespace App\Command;

use App\Stream\Channel;
use App\Stream\Client;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use React\Stream\ThroughStream;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'sse:server',
    description: 'Starts the SSE server',
)]
final class SseServerCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $channel = new Channel();
        $http = new HttpServer(function (ServerRequestInterface $request) use ($channel) {
            if ($request->getRequestTarget() === "/sse") {
                $stream = new ThroughStream();
                $client = new Client($stream);
                $channel->addClient($client);

                return new Response(
                    Response::STATUS_OK,
                    [
                        'Content-Type' => 'text/event-stream'
                    ],
                    $stream
                );
            }

            return Response::plaintext(
                "Hello World!\n"
            );
        });

        $socket = new SocketServer('0.0.0.0:8000');
        $http->listen($socket);

        $output->writeln('Server running at http://127.0.0.1:8000');

        Loop::addPeriodicTimer(1, function () use ($channel) {
            $channel->sendServerSentEvent("rand", [
                "value" => rand(0, 100)
            ]);
        });

        return 0;
    }
}

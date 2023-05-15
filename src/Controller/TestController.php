<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

final class TestController
{
    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        return new Response('test');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
final class TestController extends AbstractController
{

    public function __construct(
        private readonly Environment $twig,
    ) {
    }


    #[Route('/', name: 'test')]
    public function test(): Response
    {
        return new Response($this->twig->render('test.html.twig'));
    }
}

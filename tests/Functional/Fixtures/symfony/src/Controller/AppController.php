<?php

namespace Joli\SeoOverride\Tests\Functional\Fixtures\symfony\src\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AppController extends AbstractController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function templateAction(string $template): Response
    {
        return new Response($this->twig->render($template), 200);
    }

    public function errorAction(): Response
    {
        return new Response($this->twig->render('error.html.twig'), 400);
    }

    public function downloadAction(): BinaryFileResponse
    {
        $tmpfname = tempnam('/tmp', 'FOO');

        return new BinaryFileResponse($tmpfname);
    }

    public function adminAction(): Response
    {
        return new Response($this->twig->render('admin.html.twig'), 200);
    }
}

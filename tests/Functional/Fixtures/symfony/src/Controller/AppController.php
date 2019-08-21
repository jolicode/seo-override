<?php

namespace Joli\SeoOverride\Tests\Functional\Fixtures\symfony\src\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    public function templateAction(string $template)
    {
        return new Response($this->renderView($template), 200);
    }

    public function errorAction()
    {
        return new Response($this->renderView('error.html.twig'), 400);
    }

    public function downloadAction()
    {
        $tmpfname = tempnam('/tmp', 'FOO');

        return new BinaryFileResponse($tmpfname);
    }

    public function adminAction()
    {
        return new Response($this->renderView('admin.html.twig'), 200);
    }
}

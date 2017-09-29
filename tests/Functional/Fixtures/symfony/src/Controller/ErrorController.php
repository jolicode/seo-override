<?php

namespace Joli\SeoOverride\Tests\Functional\Fixtures\symfony\src\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    public function errorAction()
    {
        return new Response($this->renderView('error.html.twig'), 400);
    }

    public function downloadAction()
    {
        $tmpfname = tempnam('/tmp', 'FOO');

        return new BinaryFileResponse($tmpfname);
    }
}

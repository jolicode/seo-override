<?php

namespace Joli\SeoOverride\tests\Functional\Fixtures\symfony\src\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    public function errorAction()
    {
        return new Response($this->renderView('error.html.twig'), 400);
    }
}

<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\tests\Functional;

use Joli\SeoOverride\tests\Functional\Fixtures\symfony\app\AppKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class SymfonyTest extends TestCase
{
    public function test_it_overrides_seo_for_known_domain()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for homepage of domain1</title>
        <meta name="description" content="description for index" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/', 'domain1.com');

        $this->assertSame($expected, $response->getContent());

        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for another page of domain1</title>
        <meta name="description" content="description for another page" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/another-route', 'domain1.com');

        $this->assertSame($expected, $response->getContent());
    }

    public function test_it_overrides_seo_for_catch_all_domain()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for homepage of catch-all domain</title>
        <meta name="description" content="description for index" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/', 'domain2.com');

        $this->assertSame($expected, $response->getContent());

        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for another page of catch-all domain</title>
        <meta name="description" content="description for another page" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/another-route', 'domain2.com');

        $this->assertSame($expected, $response->getContent());
    }

    private function call($uri, $host)
    {
        $request = Request::create($uri, 'GET', [], [], [], [
            'HTTP_HOST' => $host,
        ]);
        $kernel = new AppKernel('test', true);

        return $kernel->handle($request);
    }
}

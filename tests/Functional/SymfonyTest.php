<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Joli\SeoOverride\Bridge\Doctrine\Entity\Seo as DoctrineSeo;
use Joli\SeoOverride\Bridge\Doctrine\Entity\SeoOverride;
use Joli\SeoOverride\Tests\Functional\Fixtures\symfony\app\AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SymfonyTest extends KernelTestCase
{
    const NOT_OVERRIDDEN_HOMEPAGE_CONTENT = <<<'HTML'
<html>
    <head>
        <title>old title for homepage</title>
        <meta name="description" content="description for homepage" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $databasePath = static::$kernel->getProjectDir() . '/data/data.sqlite';
        if (file_exists($databasePath)) {
            unlink($databasePath);
        }

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $command = $application->find('doctrine:database:create');
        $command->run(new ArrayInput([
            '--quiet' => true,
        ]), new NullOutput());
        $command = $application->find('doctrine:schema:update');
        $command->run(new ArrayInput([
            '--force' => true,
            '--quiet' => true,
        ]), new NullOutput());

        $seo = new DoctrineSeo();
        $seo->setTitle('new title for homepage of domain_doctrine');

        $seoOverride = new SeoOverride();
        $seoOverride->setPath('/');
        $seoOverride->setDomainAlias('domain_doctrine');
        $seoOverride->setSeo($seo);

        /** @var EntityManager $manager */
        $manager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $manager->persist($seoOverride);
        $manager->flush();
    }

    public function testItOverridesSeoHandledWithDoctrineFetcher()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for homepage of domain_doctrine</title>
        <meta name="description" content="description for homepage" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/', 'domain_doctrine.com');

        $this->assertSame($expected, $response->getContent());
    }

    public function testItOverridesSeoHandledWithInMemoryFetcher()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for homepage of domain_in_memory</title>
        <meta name="description" content="description for homepage" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/', 'domain_in_memory.com');

        $this->assertSame($expected, $response->getContent());
    }

    public function testItOverridesSeoHandledWithPhpFetcher()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>new title for homepage of domain_php</title>
        <meta name="description" content="description for homepage" />
    </head>
    <body>
        <h1>Hello world</h1>
    </body>
</html>

HTML;

        $response = $this->call('/', 'domain_php.com');

        $this->assertSame($expected, $response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenNoFetcherMatching()
    {
        $response = $this->call('/', 'domain_unknown.com');

        $this->assertSame(self::NOT_OVERRIDDEN_HOMEPAGE_CONTENT, $response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenNoContentOrBinaryResponse()
    {
        $response = $this->call('/download', 'localhost');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertFalse($response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenNo2XXResponse()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>old title for error page</title>
        <meta name="description" content="description for error page" />
    </head>
    <body>
        <h1>Hello error</h1>
    </body>
</html>

HTML;

        $response = $this->call('/error', 'localhost');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame($expected, $response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenRequestPathDoesNotMatch()
    {
        $expected = <<<'HTML'
<html>
    <head>
        <title>old title for admin</title>
        <meta name="description" content="description for admin" />
    </head>
    <body>
        <h1>Hello admin</h1>
    </body>
</html>

HTML;

        $response = $this->call('/admin', 'localhost');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($expected, $response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenRequestUseNotAllowedAction()
    {
        $response = $this->call('/', 'localhost', 'PUT');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(self::NOT_OVERRIDDEN_HOMEPAGE_CONTENT, $response->getContent());
    }

    public function testItDoesNotOverrideSeoWhenRequestIsXhr()
    {
        $response = $this->call('/', 'localhost', 'GET', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(self::NOT_OVERRIDDEN_HOMEPAGE_CONTENT, $response->getContent());
    }

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    private function call(string $uri, string $host, string $method = 'GET', array $server = []): Response
    {
        $server['HTTP_HOST'] = $host;

        $request = Request::create($uri, $method, [], [], [], $server);

        if (null === static::$kernel) {
            static::bootKernel();
        }

        return static::$kernel->handle($request);
    }
}

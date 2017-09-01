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
use Symfony\Component\HttpFoundation\Request;

class SymfonyTest extends KernelTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $databasePath = self::$kernel->getRootDir().'/data/data.sqlite';
        if (file_exists($databasePath)) {
            unlink($databasePath);
        }

        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->run(new ArrayInput([
            'doctrine:database:create',
            '--quiet' => true,
        ]));
        $application->run(new ArrayInput([
            'doctrine:schema:update',
            '--force' => true,
            '--quiet' => true,
        ]));

        $seo = new DoctrineSeo();
        $seo->setTitle('new title for homepage of domain_doctrine');

        $seoOverride = new SeoOverride();
        $seoOverride->setPath('/');
        $seoOverride->setDomainAlias('domain_doctrine');
        $seoOverride->setSeo($seo);

        /** @var EntityManager $manager */
        $manager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $manager->persist($seoOverride);
        $manager->flush();
    }

    public function test_it_overrides_seo_handled_with_doctrine_fetcher()
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

    public function test_it_overrides_seo_handled_with_in_memory_fetcher()
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

    public function test_it_overrides_seo_handled_with_php_fetcher()
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

    public function test_it_does_not_override_seo_when_no_fetcher_matching()
    {
        $expected = <<<'HTML'
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

        $response = $this->call('/', 'domain_unkown.com');

        $this->assertSame($expected, $response->getContent());
    }

    public function test_it_does_not_override_seo_when_no_2XX_response()
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

    public function test_it_does_not_override_seo_when_no_content_or_binary_response()
    {
        $response = $this->call('/download', 'localhost');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('🎅', $response->getContent());
    }

    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    private function call($uri, $host)
    {
        $request = Request::create($uri, 'GET', [], [], [], [
            'HTTP_HOST' => $host,
        ]);

        return self::$kernel->handle($request);
    }
}

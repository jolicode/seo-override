<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\tests\Unit\Bridge\Symfony\DependencyInjection;

use Joli\SeoOverride\Bridge\Symfony\DependencyInjection\SeoOverrideExtension;
use Joli\SeoOverride\SeoManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SeoOverrideExtensionTest extends TestCase
{
    /** @var SeoOverrideExtension */
    private $extension;

    public function setUp()
    {
        parent::setUp();

        $this->extension = new SeoOverrideExtension();
    }

    public function test_it_has_a_default_configuration()
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        self::assertTrue($container->hasParameter('seo_override.fetchers_configuration'));
        self::assertSame([], $container->getParameter('seo_override.fetchers_configuration'));

        self::assertTrue($container->hasParameter('seo_override.domains'));
        self::assertSame([], $container->getParameter('seo_override.domains'));

        self::assertTrue($container->has('seo_override.manager'));
        self::assertInstanceOf(SeoManager::class, $container->get('seo_override.manager'));

        self::assertEmpty($container->getDefinition('seo_override.manager')->getArgument(0));
        self::assertSame('%seo_override.domains%', $container->getDefinition('seo_override.manager')->getArgument(1));
    }

    public function test_it_supports_fetcher_configuration()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'fetchers' => [
                    [
                        'type' => 'fetcher1',
                        'option1' => 'hello world',
                    ],
                ],
            ],
        ], $container);

        self::assertTrue($container->hasParameter('seo_override.fetchers_configuration'));

        $fetchers = $container->getParameter('seo_override.fetchers_configuration');

        self::assertInternalType('array', $fetchers);
        self::assertCount(1, $fetchers);

        self::assertInternalType('array', $fetchers[0]);

        self::assertArrayHasKey('type', $fetchers[0]);
        self::assertSame('fetcher1', $fetchers[0]['type']);

        self::assertArrayHasKey('option1', $fetchers[0]);
        self::assertSame('hello world', $fetchers[0]['option1']);
    }

    public function test_it_preserves_fetchers_order()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'fetchers' => [
                    [
                        'type' => 'fetcher1',
                        'option1' => [],
                    ],
                    [
                        'type' => 'fetcher2',
                    ],
                    [
                        'type' => 'fetcher3',
                        'option3' => 'yolo',
                    ],
                ],
            ],
        ], $container);

        $fetchers = $container->getParameter('seo_override.fetchers_configuration');

        self::assertInternalType('array', $fetchers);
        self::assertCount(3, $fetchers);

        self::assertInternalType('array', $fetchers[0]);
        self::assertInternalType('array', $fetchers[1]);
        self::assertInternalType('array', $fetchers[2]);

        self::assertArrayHasKey('type', $fetchers[0]);
        self::assertSame('fetcher1', $fetchers[0]['type']);

        self::assertArrayHasKey('type', $fetchers[1]);
        self::assertSame('fetcher2', $fetchers[1]['type']);

        self::assertArrayHasKey('type', $fetchers[2]);
        self::assertSame('fetcher3', $fetchers[2]['type']);
    }

    public function test_it_supports_short_syntax_for_fetcher()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'fetchers' => [
                    'fetcher1',
                ],
            ],
        ], $container);

        $fetchers = $container->getParameter('seo_override.fetchers_configuration');

        self::assertInternalType('array', $fetchers);
        self::assertCount(1, $fetchers);

        self::assertInternalType('array', $fetchers[0]);

        self::assertArrayHasKey('type', $fetchers[0]);
        self::assertSame('fetcher1', $fetchers[0]['type']);
    }

    public function test_it_does_not_load_doctrine_service_when_fetcher_not_configured()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'fetchers' => [
                    'fetcher1',
                ],
            ],
        ], $container);

        self::assertFalse($container->has('seo_override.fetcher.doctrine'));
    }

    public function test_it_loads_doctrine_service_when_fetcher_configured()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'fetchers' => [
                    'doctrine',
                ],
            ],
        ], $container);

        self::assertTrue($container->has('seo_override.fetcher.doctrine'));
    }

    public function test_it_supports_domains_configuration()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'domains' => [
                    'domain1' => '@example.fr@',
                ],
            ],
        ], $container);

        self::assertTrue($container->hasParameter('seo_override.domains'));

        $parameters = $container->getParameter('seo_override.domains');

        self::assertTrue(is_array($parameters));
        self::assertCount(1, $parameters);
        self::assertArrayHasKey('domain1', $parameters);
        self::assertSame('@example.fr@', $parameters['domain1']);
    }

    public function test_it_preserves_domains_order()
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            'seo_override' => [
                'domains' => [
                    'domain1' => '@example.fr@',
                    'domain2' => '@example.com@',
                    'domain3' => '@example.es@',
                ],
            ],
        ], $container);

        self::assertTrue($container->hasParameter('seo_override.domains'));

        $parameters = $container->getParameter('seo_override.domains');

        self::assertTrue(is_array($parameters));
        self::assertCount(3, $parameters);
        self::assertArrayHasKey('domain1', $parameters);
        self::assertArrayHasKey('domain2', $parameters);
        self::assertArrayHasKey('domain3', $parameters);
        self::assertSame('@example.fr@', $parameters['domain1']);
        self::assertSame('@example.com@', $parameters['domain2']);
        self::assertSame('@example.es@', $parameters['domain3']);
        self::assertSame('@example.fr@', array_shift($parameters));
        self::assertSame('@example.com@', array_shift($parameters));
        self::assertSame('@example.es@', array_shift($parameters));
    }
}

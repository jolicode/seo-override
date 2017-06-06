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

use Joli\SeoOverride\Bridge\Symfony\DependencyInjection\CompilerPass\RegisterFetcherPass;
use Joli\SeoOverride\tests\Unit\Fixtures\FakeFetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class RegisterFetcherPassTest extends TestCase
{
    /** @var RegisterFetcherPass */
    private $compilerPass;

    /** @var ObjectProphecy */
    private $container;

    /** @var ParameterBag */
    private $parameterBag;

    /** @var ObjectProphecy */
    private $managerDefinition;

    public function setUp()
    {
        parent::setUp();

        $this->compilerPass = new RegisterFetcherPass();
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->parameterBag = $this->prophesize(ParameterBag::class);
        $this->managerDefinition = $this->prophesize(Definition::class);

        $this->container->getParameterBag()->willReturn($this->parameterBag->reveal());
    }

    public function test_it_throws_exception_with_tag_not_defining_alias()
    {
        $tags = [
            'service1' => [
                [],
            ],
            'service2' => [
                [],
            ],
        ];

        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('These fetcher services do not define an "alias" attribute on their "seo_override.fetcher" tags: service1, service2', $e->getMessage());
        }
    }

    public function test_it_throws_exception_with_alias_used_multiple_times()
    {
        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
            'service2' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
        ];

        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Fetcher aliases should be unique. The "fetcher1" alias is defined by these fetchers: service1, service2', $e->getMessage());
        }
    }

    public function test_it_accepts_no_configured_fetchers()
    {
        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
        ];

        $this->managerDefinition->replaceArgument(0, [])->shouldBeCalled();

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn([]);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);
        $this->container->getDefinition('seo_override.manager')->willReturn($this->managerDefinition->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(false);
        $this->parameterBag->remove('seo_override.fetchers_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }

    public function test_it_throws_exception_with_unknown_fetcher()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher2',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
        ];

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Unkown "fetcher2" fetcher. Available fetchers are: fetcher1', $e->getMessage());
        }
    }

    public function test_it_throws_exception_when_missing_mandatory_option_for_fetcher()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher1',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                    'required_options' => 'option1',
                ],
            ],
        ];

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('The "option1" option is mandatory for fetcher "fetcher1"', $e->getMessage());
        }
    }

    public function test_it_configures_option_for_fetcher()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher1',
                'requiredOption' => 'yolo',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                    'required_options' => 'requiredOption',
                ],
            ],
        ];

        $fetcherDefinition = $this->prophesize(Definition::class);
        $fetcherDefinition->getClass()->willReturn(FakeFetcher::class);
        $fetcherDefinition->replaceArgument(0, 'yolo')->shouldBeCalled();

        $this->managerDefinition->replaceArgument(0, [$fetcherDefinition])->shouldBeCalled();

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);
        $this->container->getDefinition('seo_override.manager')->willReturn($this->managerDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($fetcherDefinition->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(false);
        $this->parameterBag->remove('seo_override.fetchers_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }

    public function test_it_throws_exception_with_unknown_option_for_fetcher()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher1',
                'unknownOption' => 'world',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
        ];

        $fetcherDefinition = $this->prophesize(Definition::class);
        $fetcherDefinition->getClass()->willReturn(FakeFetcher::class);

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);
        $this->container->getDefinition('seo_override.manager')->willReturn($this->managerDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($fetcherDefinition->reveal());

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Unkown "unknownOption" option for fetcher "fetcher1"', $e->getMessage());
        }
    }

    public function test_it_preserves_fetcher_order()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher1',
                'requiredOption' => '1',
            ],
            [
                'type' => 'fetcher2',
                'requiredOption' => '2',
            ],
            [
                'type' => 'fetcher3',
                'requiredOption' => '3',
            ],
        ];

        $tags = [
            'service2' => [
                [
                    'alias' => 'fetcher2',
                    'required_options' => 'requiredOption',
                ],
            ],
            'service1' => [
                [
                    'alias' => 'fetcher1',
                    'required_options' => 'requiredOption',
                ],
            ],
            'service3' => [
                [
                    'alias' => 'fetcher3',
                    'required_options' => 'requiredOption',
                ],
            ],
            'service4' => [
                [
                    'alias' => 'fetcher4',
                    'required_options' => 'requiredOption',
                ],
            ],
        ];

        $fetcherDefinition1 = $this->prophesize(Definition::class);
        $fetcherDefinition1->getClass()->willReturn(FakeFetcher::class);
        $fetcherDefinition1->replaceArgument(0, '1')->shouldBeCalled();

        $fetcherDefinition2 = $this->prophesize(Definition::class);
        $fetcherDefinition2->getClass()->willReturn(FakeFetcher::class);
        $fetcherDefinition2->replaceArgument(0, '2')->shouldBeCalled();

        $fetcherDefinition3 = $this->prophesize(Definition::class);
        $fetcherDefinition3->getClass()->willReturn(FakeFetcher::class);
        $fetcherDefinition3->replaceArgument(0, '3')->shouldBeCalled();

        $this->managerDefinition->replaceArgument(0, [$fetcherDefinition1, $fetcherDefinition2, $fetcherDefinition3])->shouldBeCalled();

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);
        $this->container->getDefinition('seo_override.manager')->willReturn($this->managerDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($fetcherDefinition1->reveal());
        $this->container->getDefinition('service2')->willReturn($fetcherDefinition2->reveal());
        $this->container->getDefinition('service3')->willReturn($fetcherDefinition3->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(false);
        $this->parameterBag->remove('seo_override.fetchers_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }

    public function test_it_defines_fetcher_mapping_when_in_debug()
    {
        $fetchersConfiguration = [
            [
                'type' => 'fetcher1',
            ],
            [
                'type' => 'fetcher2',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'fetcher1',
                ],
            ],
            'service2' => [
                [
                    'alias' => 'fetcher2',
                ],
            ],
        ];

        $fetcherDefinition1 = $this->prophesize(Definition::class);
        $fetcherDefinition1->getClass()->willReturn(FakeFetcher::class);

        $fetcherDefinition2 = $this->prophesize(Definition::class);
        $fetcherDefinition2->getClass()->willReturn(FakeFetcher::class);

        $this->managerDefinition->replaceArgument(0, [$fetcherDefinition1, $fetcherDefinition2])->shouldBeCalled();

        $this->container->getParameter('seo_override.fetchers_configuration')->willReturn($fetchersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.fetcher')->willReturn($tags);
        $this->container->getDefinition('seo_override.manager')->willReturn($this->managerDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($fetcherDefinition1->reveal());
        $this->container->getDefinition('service2')->willReturn($fetcherDefinition2->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(true);
        $this->container->getParameter('kernel.debug')->willReturn(true);
        $this->container->setParameter('seo_override.fetchers_mapping', [
            'fetcher1' => FakeFetcher::class,
            'fetcher2' => FakeFetcher::class,
        ])->shouldBeCalled();
        $this->parameterBag->remove('seo_override.fetchers_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }
}

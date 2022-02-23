<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Unit\Bridge\Symfony\DependencyInjection;

use Joli\SeoOverride\Bridge\Symfony\DependencyInjection\CompilerPass\RegisterBlacklisterPass;
use Joli\SeoOverride\Tests\Unit\Fixtures\FakeBlacklister;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class RegisterBlacklisterPassTest extends TestCase
{
    /** @var RegisterBlacklisterPass */
    private $compilerPass;

    /** @var ObjectProphecy */
    private $container;

    /** @var ParameterBag */
    private $parameterBag;

    /** @var ObjectProphecy */
    private $chainBlacklisterDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compilerPass = new RegisterBlacklisterPass();
        $this->container = $this->prophesize(ContainerBuilder::class);
        $this->parameterBag = $this->prophesize(ParameterBag::class);
        $this->chainBlacklisterDefinition = $this->prophesize(Definition::class);

        $this->container->getParameterBag()->willReturn($this->parameterBag->reveal());
    }

    public function testItThrowsExceptionWithTagNotDefiningAlias()
    {
        $tags = [
            'service1' => [
                [],
            ],
            'service2' => [
                [],
            ],
        ];

        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('These blacklister services do not define an "alias" attribute on their "seo_override.blacklister" tags: service1, service2', $e->getMessage());
        }
    }

    public function testItThrowsExceptionWithAliasUsedMultipleTimes()
    {
        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
            'service2' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
        ];

        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Blacklister aliases should be unique. The "blacklister1" alias is defined by these blacklisters: service1, service2', $e->getMessage());
        }
    }

    public function testItAcceptsNoConfiguredBlacklisters()
    {
        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
        ];

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn([]);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);
        $this->container->getDefinition('seo_override.subscriber')->willReturn($this->chainBlacklisterDefinition->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(false);
        $this->container->setAlias('seo_override.blacklister.default', 'seo_override.blacklister.null')->shouldBeCalled();
        $this->parameterBag->remove('seo_override.blacklisters_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }

    public function testItThrowsExceptionWithUnknownBlacklister()
    {
        $blacklistersConfiguration = [
            [
                'type' => 'blacklister2',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
        ];

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn($blacklistersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Unknown "blacklister2" blacklister. Available blacklisters are: blacklister1', $e->getMessage());
        }
    }

    public function testItThrowsExceptionWhenMissingMandatoryOptionForBlacklister()
    {
        $blacklistersConfiguration = [
            [
                'type' => 'blacklister1',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                    'required_options' => 'option1',
                ],
            ],
        ];

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn($blacklistersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('The "option1" option is mandatory for blacklister "blacklister1"', $e->getMessage());
        }
    }

    public function testItConfiguresOptionForBlacklister()
    {
        $blacklistersConfiguration = [
            [
                'type' => 'blacklister1',
                'requiredOption' => 'yolo',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                    'required_options' => 'requiredOption',
                ],
            ],
        ];

        $blacklisterDefinition = $this->prophesize(Definition::class);
        $blacklisterDefinition->getClass()->willReturn(FakeBlacklister::class);
        $blacklisterDefinition->replaceArgument(0, 'yolo')->shouldBeCalled();

        $this->chainBlacklisterDefinition->replaceArgument(0, [$blacklisterDefinition])->shouldBeCalled();

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn($blacklistersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);
        $this->container->getDefinition('seo_override.blacklister.chain')->willReturn($this->chainBlacklisterDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($blacklisterDefinition->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(false);
        $this->container->setAlias('seo_override.blacklister.default', 'seo_override.blacklister.chain')->shouldBeCalled();
        $this->parameterBag->remove('seo_override.blacklisters_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }

    public function testItThrowsExceptionWithUnknownOptionForBlacklister()
    {
        $blacklistersConfiguration = [
            [
                'type' => 'blacklister1',
                'unknownOption' => 'world',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
        ];

        $blacklisterDefinition = $this->prophesize(Definition::class);
        $blacklisterDefinition->getClass()->willReturn(FakeBlacklister::class);

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn($blacklistersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);
        $this->container->getDefinition('seo_override.blacklister.chain')->willReturn($this->chainBlacklisterDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($blacklisterDefinition->reveal());

        try {
            $this->compilerPass->process($this->container->reveal());
            self::fail('An exception should have been thrown');
        } catch (LogicException $e) {
            self::assertSame('Unknown "unknownOption" option for blacklister "blacklister1"', $e->getMessage());
        }
    }

    public function testItDefinesBlacklisterMappingWhenInDebug()
    {
        $blacklistersConfiguration = [
            [
                'type' => 'blacklister1',
            ],
            [
                'type' => 'blacklister2',
            ],
        ];

        $tags = [
            'service1' => [
                [
                    'alias' => 'blacklister1',
                ],
            ],
            'service2' => [
                [
                    'alias' => 'blacklister2',
                ],
            ],
        ];

        $blacklisterDefinition1 = $this->prophesize(Definition::class);
        $blacklisterDefinition1->getClass()->willReturn(FakeBlacklister::class);

        $blacklisterDefinition2 = $this->prophesize(Definition::class);
        $blacklisterDefinition2->getClass()->willReturn(FakeBlacklister::class);

        $this->chainBlacklisterDefinition->replaceArgument(0, [$blacklisterDefinition1, $blacklisterDefinition2])->shouldBeCalled();

        $this->container->getParameter('seo_override.blacklisters_configuration')->willReturn($blacklistersConfiguration);
        $this->container->findTaggedServiceIds('seo_override.blacklister')->willReturn($tags);
        $this->container->getDefinition('seo_override.blacklister.chain')->willReturn($this->chainBlacklisterDefinition->reveal());
        $this->container->getDefinition('service1')->willReturn($blacklisterDefinition1->reveal());
        $this->container->getDefinition('service2')->willReturn($blacklisterDefinition2->reveal());
        $this->container->hasParameter('kernel.debug')->willReturn(true);
        $this->container->getParameter('kernel.debug')->willReturn(true);
        $this->container->setParameter('seo_override.blacklisters_mapping', [
            'blacklister1' => FakeBlacklister::class,
            'blacklister2' => FakeBlacklister::class,
        ])->shouldBeCalled();
        $this->container->setAlias('seo_override.blacklister.default', 'seo_override.blacklister.chain')->shouldBeCalled();
        $this->parameterBag->remove('seo_override.blacklisters_configuration')->shouldBeCalled();

        $this->compilerPass->process($this->container->reveal());
    }
}

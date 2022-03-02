<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Unit\Bridge\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Joli\SeoOverride\Bridge\Doctrine\DoctrineFetcher;
use Joli\SeoOverride\Bridge\Doctrine\Entity\Seo;
use Joli\SeoOverride\Bridge\Doctrine\Entity\SeoOverride;
use Joli\SeoOverride\Bridge\Doctrine\Repository\SeoOverrideRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineFetcherTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy */
    private $registry;

    /** @var ObjectProphecy */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->repository = $this->prophesize(SeoOverrideRepository::class);
        $manager = $this->prophesize(EntityManager::class);

        $this->registry->getManagerForClass(SeoOverride::class)->willReturn($manager->reveal());
        $manager->getRepository(SeoOverride::class)->willReturn($this->repository->reveal());
    }

    public function testItReturnsNullWithNoMatchingOverridesForGivenDomain()
    {
        $this->repository->findOneForPathAndDomain('/url1', 'domain_alias_1')->willReturn(null);

        $fetcher = new DoctrineFetcher($this->registry->reveal());

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
    }

    public function testItReturnsNullWithNoMatchingOverridesForCatchAllDomain()
    {
        $this->repository->findOneForPathAndDomain('/url1', null)->willReturn(null);

        $fetcher = new DoctrineFetcher($this->registry->reveal());

        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function testItReturnsSeoWithMatchingOverrides()
    {
        $expectedSeo = new Seo();
        $seoOverride = new SeoOverride();
        $seoOverride->setSeo($expectedSeo);

        $this->repository->findOneForPathAndDomain('/url1', null)->willReturn($seoOverride);

        $fetcher = new DoctrineFetcher($this->registry->reveal());

        $seo = $fetcher->fetch('/url1', null);

        self::assertNotNull($seo);
        self::assertSame($expectedSeo, $seo);
    }
}

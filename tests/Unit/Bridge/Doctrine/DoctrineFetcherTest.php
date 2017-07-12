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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Joli\SeoOverride\Bridge\Doctrine\DoctrineFetcher;
use Joli\SeoOverride\Bridge\Doctrine\Entity\Seo;
use Joli\SeoOverride\Bridge\Doctrine\Entity\SeoOverride;
use Joli\SeoOverride\Bridge\Doctrine\Repository\SeoOverrideRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class DoctrineFetcherTest extends TestCase
{
    /** @var ObjectProphecy */
    private $registry;

    /** @var ObjectProphecy */
    private $repository;

    public function setUp()
    {
        parent::setUp();

        $this->registry = $this->prophesize(ManagerRegistry::class);
        $this->repository = $this->prophesize(SeoOverrideRepository::class);
        $manager = $this->prophesize(EntityManager::class);

        $this->registry->getManagerForClass(SeoOverride::class)->willReturn($manager->reveal());
        $manager->getRepository(SeoOverride::class)->willReturn($this->repository->reveal());
    }

    public function test_it_returns_null_with_no_matching_overrides_for_given_domain()
    {
        $this->repository->findOneForPathAndDomain('/url1', 'domain_alias_1')->willReturn(null);

        $fetcher = new DoctrineFetcher($this->registry->reveal());

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
    }

    public function test_it_returns_null_with_no_matching_overrides_for_catch_all_domain()
    {
        $this->repository->findOneForPathAndDomain('/url1', null)->willReturn(null);

        $fetcher = new DoctrineFetcher($this->registry->reveal());

        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function test_it_returns_seo_with_matching_overrides()
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

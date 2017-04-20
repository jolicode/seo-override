<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Joli\SeoOverride\Bridge\Doctrine\Entity\SeoOverride;
use Joli\SeoOverride\Fetcher;

class DoctrineFetcher implements Fetcher
{
    /** @var ManagerRegistry */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function fetch(string $path, string $domainAlias = null)
    {
        $repository = $this->registry->getManagerForClass(SeoOverride::class)->getRepository(SeoOverride::class);

        /** @var SeoOverride|null $seoOverride */
        $seoOverride = $repository->findOneForPathAndDomain($path, $domainAlias);

        return $seoOverride ? $seoOverride->getSeo() : null;
    }
}

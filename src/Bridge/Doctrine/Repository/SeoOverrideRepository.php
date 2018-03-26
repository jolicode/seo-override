<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Joli\SeoOverride\Bridge\Doctrine\Entity\SeoOverride;

class SeoOverrideRepository extends EntityRepository
{
    /**
     * @return SeoOverride|null
     */
    public function findOneForPathAndDomain(string $path, string $domainAlias = null)
    {
        return $this->createQueryBuilder('s')
             ->andWhere('s.hashedPath = :hashedPath')
             ->setParameter('hashedPath', sha1($path))
             ->andWhere('s.domainAlias = :domainAlias')
             ->setParameter('domainAlias', $domainAlias)
             ->setMaxResults(1)
             ->getQuery()
             ->getOneOrNullResult()
        ;
    }
}

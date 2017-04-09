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
     * @param string $path
     *
     * @return SeoOverride|null
     */
    public function findOneForPath(string $path)
    {
        return $this->createQueryBuilder('s')
             ->andWhere('s.path = :path')
             ->setParameter('path', $path)
             ->setMaxResults(1)
             ->getQuery()
             ->getOneOrNullResult();
    }
}

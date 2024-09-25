<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Doctrine\Entity;

class SeoOverride
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $hashedPath;

    /**
     * @var string|null
     */
    private $domainAlias;

    /**
     * @var Seo
     */
    private $seo;

    public function __construct()
    {
        $this->seo = new Seo;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
        $this->hashedPath = sha1($path);
    }

    public function getHashedPath()
    {
        return $this->hashedPath;
    }

    public function getDomainAlias()
    {
        return $this->domainAlias;
    }

    public function setDomainAlias(string $domainAlias = null)
    {
        $this->domainAlias = $domainAlias;
    }

    public function getSeo()
    {
        return $this->seo;
    }

    public function setSeo(Seo $seo)
    {
        $this->seo = $seo;
    }
}

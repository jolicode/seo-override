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

use Joli\SeoOverride\Seo;

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
     * @var string|null
     */
    private $domain;

    /**
     * @var Seo
     */
    private $seo;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain(string $domain = null)
    {
        $this->domain = $domain;
    }

    public function getSeo(): Seo
    {
        return $this->seo;
    }

    public function setSeo(Seo $seo)
    {
        $this->seo = $seo;
    }
}

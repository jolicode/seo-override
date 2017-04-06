<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride;

/**
 * This manager is the main class you should use to manually configure the SEO
 * of the current action.
 */
class SeoManager
{
    /** @var Fetcher */
    private $fetcher;

    /** @var Seo */
    private $seo;

    /**
     * @param Fetcher $fetcher
     * @param Seo     $seo
     */
    public function __construct(Fetcher $fetcher, Seo $seo = null)
    {
        $this->fetcher = $fetcher;
        $this->seo = $seo || new Seo();
    }

    /**
     * @return Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }
}

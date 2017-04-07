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
    /** @var Fetcher[] */
    private $fetchers;

    /** @var Seo */
    private $seo;

    /**
     * @param Fetcher[] $fetchers
     * @param Seo       $seo
     */
    public function __construct(array $fetchers, Seo $seo = null)
    {
        $this->fetchers = $fetchers;
        $this->seo = $seo || new Seo();
    }

    /**
     * @return Seo
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Update the Seo from the fetchers for a specific path.
     *
     * @param mixed $path
     *
     * @return Seo
     */
    public function fetchSeoForPath($path)
    {
        foreach ($this->fetchers as $fetcher) {
            if ($seo = $fetcher->fetch($path)) {
                $this->mergeSeo($seo);

                break;
            }
        }

        return $this->seo;
    }

    /**
     * Perform the override of HTML SEO related tags.
     *
     * @param $html
     *
     * @return bool|Seo
     */
    public function overrideHtml($html)
    {
        // @todo

        $seo = $this->getSeo();

        if ($seo->getTitle()) {
            $html = preg_replace(
                '@<!--SEO_TITLE-->.+<!--/SEO_TITLE-->@im',
                '<title>'.htmlspecialchars($seo->getTitle()).'</title>',
                $html
            );
        }

        if ($seo->getDescription()) {
            $html = preg_replace(
                '@<!--SEO_DESC-->.*?<!--/SEO_DESC-->@im',
                '<meta name="description" content="'.htmlspecialchars($seo->getDescription()).'">',
                $html
            );
        }

        if ($seo->getKeywords()) {
            $html = preg_replace(
                '@<!--SEO_KEYWORDS-->.*?<!--/SEO_KEYWORDS-->@im',
                '<meta name="description" content="'.htmlspecialchars($seo->getKeywords()).'">',
                $html
            );
        }

        if ($seo->getRobots()) {
            $html = preg_replace(
                '@<!--SEO_ROBOT-->.*?<!--/SEO_ROBOT-->@im',
                '<meta name="robots" content="'.htmlspecialchars($seo->getRobots()).'">',
                $html
            );
        }

        if ($seo->getCanonical()) {
            $html = preg_replace(
                '@<!--SEO_CANO-->.*?<!--/SEO_CANO-->@im',
                '<link rel="canonical" href="'.htmlspecialchars($seo->getCanonical()).'" />',
                $html
            );
        }

        if ($seo->getOgTitle()) {
            $html = preg_replace(
                '@<!--SEO_OGTITLE-->.*?<!--/SEO_OGTITLE-->@im',
                '<meta property="og:title" content="'.htmlspecialchars($seo->getOgTitle()).'" />',
                $html
            );
        }

        if ($seo->getOgDescription()) {
            $html = preg_replace(
                '@<!--SEO_OGDESC-->.*?<!--/SEO_OGDESC-->@im',
                '<meta property="og:description" content="'.htmlspecialchars($seo->getOgDescription()).'" />',
                $html
            );
        }

        return $html;
    }

    /**
     * Merge the given Seo data inside the current one.
     *
     * @param Seo $seo
     */
    private function mergeSeo(Seo $seo)
    {
        if ($seo->getTitle()) {
            $this->seo->setTitle($seo->getTitle());
        }
        if ($seo->getDescription()) {
            $this->seo->setDescription($seo->getDescription());
        }
        if ($seo->getKeywords()) {
            $this->seo->setKeywords($seo->getKeywords());
        }
        if ($seo->getRobots()) {
            $this->seo->setRobots($seo->getRobots());
        }
        if ($seo->getCanonical()) {
            $this->seo->setCanonical($seo->getCanonical());
        }
        if ($seo->getOgTitle()) {
            $this->seo->setOgTitle($seo->getOgTitle());
        }
        if ($seo->getOgDescription()) {
            $this->seo->setOgDescription($seo->getOgDescription());
        }
    }
}

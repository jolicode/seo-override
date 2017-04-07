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

    /**
     * Update the Seo from the fetchers for a specific path.
     *
     * @param mixed $path
     *
     * @return Seo
     */
    public function fetchSeoForPath($path)
    {
        // @todo
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

        if ($seo->getMetaTitle()) {
            $html = preg_replace(
                '@<!--SEO_TITLE-->.+<!--/SEO_TITLE-->@im',
                '<title>'.htmlspecialchars($seo->getMetaTitle()).'</title>',
                $html
            );
        }

        if ($seo->getMetaDescription()) {
            $html = preg_replace(
                '@<!--SEO_DESC-->.*?<!--/SEO_DESC-->@im',
                '<meta name="description" content="'.htmlspecialchars($seo->getMetaDescription()).'">',
                $html
            );
        }

        if ($seo->getMetaCanonical()) {
            $html = preg_replace(
                '@<!--SEO_CANO-->.*?<!--/SEO_CANO-->@im',
                '<link rel="canonical" href="'.htmlspecialchars($seo->getMetaCanonical()).'" />',
                $html
            );
        }

        if ($seo->getMetaRobots()) {
            $html = preg_replace(
                '@<!--SEO_ROBOT-->.*?<!--/SEO_ROBOT-->@im',
                '<meta name="robots" content="'.htmlspecialchars($seo->getMetaRobots()).'">',
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
}

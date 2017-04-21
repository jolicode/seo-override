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

    /** @var string[] */
    private $domains;

    /** @var Seo */
    private $seo;

    /**
     * @param Fetcher[] $fetchers
     * @param string[]  $domains
     * @param Seo       $seo
     */
    public function __construct(array $fetchers, array $domains, Seo $seo = null)
    {
        $this->fetchers = $fetchers;
        $this->domains = $domains;
        $this->seo = $seo ?: new Seo();
    }

    public function getSeo(): Seo
    {
        return $this->seo;
    }

    /**
     * Update and override the Seo of the HTML for the given path and domain.
     */
    public function updateAndOverride(string $html, string $path, string $domain): string
    {
        $this->updateSeo($path, $domain);

        return $this->overrideHtml($html);
    }

    /**
     * Update the Seo from the fetchers for a specific path and domain.
     */
    public function updateSeo(string $path, string $domain): Seo
    {
        $domainAlias = $this->findDomainAlias($domain);

        foreach ($this->fetchers as $fetcher) {
            // Try for the requested domain if it's known
            if ($domainAlias && $seo = $fetcher->fetch($path, $domainAlias)) {
                $this->mergeSeo($seo);

                break;
            }

            // Try for the catch all domain
            if ($seo = $fetcher->fetch($path, null)) {
                $this->mergeSeo($seo);

                break;
            }
        }

        return $this->seo;
    }

    /**
     * Perform the override of HTML SEO related tags.
     */
    public function overrideHtml(string $html): string
    {
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
                '@<!--SEO_DESCRIPTION-->.*?<!--/SEO_DESCRIPTION-->@im',
                '<meta name="description" content="'.htmlspecialchars($seo->getDescription()).'" />',
                $html
            );
        }

        if ($seo->getKeywords()) {
            $html = preg_replace(
                '@<!--SEO_KEYWORDS-->.*?<!--/SEO_KEYWORDS-->@im',
                '<meta name="keywords" content="'.htmlspecialchars($seo->getKeywords()).'" />',
                $html
            );
        }

        if ($seo->getRobots()) {
            $html = preg_replace(
                '@<!--SEO_ROBOTS-->.*?<!--/SEO_ROBOTS-->@im',
                '<meta name="robots" content="'.htmlspecialchars($seo->getRobots()).'" />',
                $html
            );
        }

        if ($seo->getCanonical()) {
            $html = preg_replace(
                '@<!--SEO_CANONICAL-->.*?<!--/SEO_CANONICAL-->@im',
                '<link rel="canonical" href="'.htmlspecialchars($seo->getCanonical()).'" />',
                $html
            );
        }

        if ($seo->getOgTitle()) {
            $html = preg_replace(
                '@<!--SEO_OG_TITLE-->.*?<!--/SEO_OG_TITLE-->@im',
                '<meta property="og:title" content="'.htmlspecialchars($seo->getOgTitle()).'" />',
                $html
            );
        }

        if ($seo->getOgDescription()) {
            $html = preg_replace(
                '@<!--SEO_OG_DESCRIPTION-->.*?<!--/SEO_OG_DESCRIPTION-->@im',
                '<meta property="og:description" content="'.htmlspecialchars($seo->getOgDescription()).'" />',
                $html
            );
        }

        return $html;
    }

    /**
     * Update and override the Seo of the HTML for the given domain and path.
     *
     * @return string|null
     */
    private function findDomainAlias(string $domain)
    {
        foreach ($this->domains as $domainAlias => $pattern) {
            if (preg_match($pattern, $domain)) {
                return $domainAlias;
            }
        }

        return null;
    }

    /**
     * Merge the given Seo data inside the current one.
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

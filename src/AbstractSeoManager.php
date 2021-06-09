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

abstract class AbstractSeoManager implements SeoManagerInterface
{
    public function updateAndOverride(string $html, string $path, string $domain): string
    {
        $this->updateSeo($path, $domain);

        return $this->overrideHtml($html);
    }

    public function updateSeo(string $path, string $domain): Seo
    {
        $domainAlias = $this->findDomainAlias($domain);

        foreach ($this->getFetchers() as $fetcher) {
            $seo = $this->fetch($fetcher, $path, $domainAlias);

            if ($seo) {
                $this->mergeSeo($seo);
                break;
            }
        }

        return $this->getSeo();
    }

    public function fetch(Fetcher $fetcher, string $path, string $domainAlias = null)
    {
        // Try for the requested domain if it's known
        if ($domainAlias && $seo = $fetcher->fetch($path, $domainAlias)) {
            return $seo;
        }

        // Try for the catch all domain
        if ($seo = $fetcher->fetch($path, null)) {
            return $seo;
        }

        return null;
    }

    public function overrideHtml(string $html): string
    {
        $seo = $this->getSeo();

        return preg_replace(
            [
                '@<!--SEO_TITLE-->(.+)<!--/SEO_TITLE-->@ims',
                '@<!--SEO_DESCRIPTION-->(.*?)<!--/SEO_DESCRIPTION-->@ims',
                '@<!--SEO_KEYWORDS-->(.*?)<!--/SEO_KEYWORDS-->@ims',
                '@<!--SEO_ROBOTS-->(.*?)<!--/SEO_ROBOTS-->@ims',
                '@<!--SEO_CANONICAL-->(.*?)<!--/SEO_CANONICAL-->@ims',
                '@<!--SEO_OG_TITLE-->(.*?)<!--/SEO_OG_TITLE-->@ims',
                '@<!--SEO_OG_DESCRIPTION-->(.*?)<!--/SEO_OG_DESCRIPTION-->@ims',
            ],
            [
                $seo->getTitle() ? '<title>'.$this->encodeHtmlChars($seo->getTitle()).'</title>' : '$1',
                $seo->getDescription() ? '<meta name="description" content="'.$this->encodeHtmlChars($seo->getDescription()).'" />' : '$1',
                $seo->getKeywords() ? '<meta name="keywords" content="'.$this->encodeHtmlChars($seo->getKeywords()).'" />' : '$1',
                $seo->getRobots() ? '<meta name="robots" content="'.$this->encodeHtmlChars($seo->getRobots()).'" />' : '$1',
                $seo->getCanonical() ? '<link rel="canonical" href="'.$this->encodeHtmlChars($seo->getCanonical()).'" />' : '$1',
                $seo->getOgTitle() ? '<meta property="og:title" content="'.$this->encodeHtmlChars($seo->getOgTitle()).'" />' : '$1',
                $seo->getOgDescription() ? '<meta property="og:description" content="'.$this->encodeHtmlChars($seo->getOgDescription()).'" />' : '$1',
            ],
            $html
        );
    }

    /**
     * Update and override the Seo of the HTML for the given domain and path.
     */
    protected function findDomainAlias(string $domain)
    {
        foreach ($this->getDomains() as $domainAlias => $pattern) {
            if (preg_match('#'.$pattern.'#i', $domain)) {
                return $domainAlias;
            }
        }

        return null;
    }

    /**
     * Merge the given Seo data inside the current one.
     */
    protected function mergeSeo(Seo $seo)
    {
        if ($seo->getTitle()) {
            $this->getSeo()->setTitle($seo->getTitle());
        }
        if ($seo->getDescription()) {
            $this->getSeo()->setDescription($seo->getDescription());
        }
        if ($seo->getKeywords()) {
            $this->getSeo()->setKeywords($seo->getKeywords());
        }
        if ($seo->getRobots()) {
            $this->getSeo()->setRobots($seo->getRobots());
        }
        if ($seo->getCanonical()) {
            $this->getSeo()->setCanonical($seo->getCanonical());
        }
        if ($seo->getOgTitle()) {
            $this->getSeo()->setOgTitle($seo->getOgTitle());
        }
        if ($seo->getOgDescription()) {
            $this->getSeo()->setOgDescription($seo->getOgDescription());
        }
    }

    /**
     * Wrapper around htmlspecialchars.
     */
    protected function encodeHtmlChars(string $string): string
    {
        return htmlspecialchars($string, ENT_COMPAT | ENT_HTML401, $this->getEncoding());
    }
}

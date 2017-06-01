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

interface SeoManagerInterface
{
    /**
     * Get the current Seo object.
     */
    public function getSeo(): Seo;

    /**
     * Update and override the Seo of the HTML for the given path and domain.
     */
    public function updateAndOverride(string $html, string $path, string $domain): string;

    /**
     * Update the Seo from the fetchers for a specific path and domain.
     */
    public function updateSeo(string $path, string $domain): Seo;

    /**
     * Fetch Seo from a Fetcher.
     *
     * @return Seo|null
     */
    public function fetch(Fetcher $fetcher, string $path, $domainAlias);

    /**
     * Perform the override of HTML SEO related tags.
     */
    public function overrideHtml(string $html): string;
}

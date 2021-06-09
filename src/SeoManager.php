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
class SeoManager extends AbstractSeoManager implements SeoManagerInterface
{
    /** @var Fetcher[] */
    private $fetchers;

    /** @var string[] */
    private $domains;

    /** @var Seo */
    private $seo;

    /** @var string */
    private $encoding = 'UTF-8';

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

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function setEncoding(string $encoding)
    {
        $this->encoding = $encoding;
    }

    public function getSeo(): Seo
    {
        return $this->seo;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getFetchers(): array
    {
        return $this->fetchers;
    }
}

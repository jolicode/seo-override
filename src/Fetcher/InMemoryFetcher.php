<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Fetcher;

use Joli\SeoOverride\Fetcher;
use Joli\SeoOverride\Seo;

/**
 * This fetcher looks for overrides from the array given when instantiating it.
 *
 * The array given should looks like :
 *
 * ```php
 * [
 *     'domain_alias_1' => [
 *         '/url1' => [
 *              // All properties of Seo can optionally be defined (title, description, keywords, robots, etc)
 *             'title' => 'Title url 1',
 *             'description' => 'Description url 1' ,
 *         ],
 *         '/url2' => [
 *             'title' => 'Title url 2',
 *         ],
 *     ],
 *     // Empty alias means that every domain will match the following overrides
 *     '' => [
 *         '/url3' => [
 *             'title' => 'Title url 3',
 *         ],
 *     ],
 * ]
 * ```
 */
class InMemoryFetcher implements Fetcher
{
    /** @var array[] */
    private $overrides = [];

    public function __construct(array $overrides)
    {
        $this->overrides = $overrides;
    }

    public function fetch(string $path, string $domainAlias = null)
    {
        $domainAlias = $domainAlias ?: '';

        if (!\array_key_exists($domainAlias, $this->overrides) || !\is_array($this->overrides[$domainAlias])) {
            return null;
        }

        if (!\array_key_exists($path, $this->overrides[$domainAlias]) || !\is_array($this->overrides[$domainAlias][$path])) {
            return null;
        }

        return $this->hydrateSeo($this->overrides[$domainAlias][$path]);
    }

    private function hydrateSeo(array $array): Seo
    {
        $seo = new Seo();

        if (\array_key_exists('title', $array)) {
            $seo->setTitle($array['title']);
        }
        if (\array_key_exists('description', $array)) {
            $seo->setDescription($array['description']);
        }
        if (\array_key_exists('keywords', $array)) {
            $seo->setKeywords($array['keywords']);
        }
        if (\array_key_exists('robots', $array)) {
            $seo->setRobots($array['robots']);
        }
        if (\array_key_exists('canonical', $array)) {
            $seo->setCanonical($array['canonical']);
        }
        if (\array_key_exists('ogTitle', $array)) {
            $seo->setOgTitle($array['ogTitle']);
        }
        if (\array_key_exists('ogDescription', $array)) {
            $seo->setOgDescription($array['ogDescription']);
        }

        return $seo;
    }
}

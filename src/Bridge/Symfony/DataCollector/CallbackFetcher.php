<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Fetcher;

class CallbackFetcher implements Fetcher
{
    /** @var Fetcher */
    private $fetcher;

    /** @var callable */
    private $callback;

    public function __construct(Fetcher $fetcher, callable $callback)
    {
        $this->fetcher = $fetcher;
        $this->callback = $callback;
    }

    public function fetch(string $path, string $domainAlias = null)
    {
        $seo = $this->fetcher->fetch($path, $domainAlias);

        $callback = $this->callback;
        $callback($path, $domainAlias, $seo);

        return $seo;
    }
}

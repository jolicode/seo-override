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
 * A fetcher is responsible to find and return an eventual Seo configuration
 * for a given path.
 */
interface Fetcher
{
    /**
     * @param string $path
     *
     * @return Seo|null
     */
    public function fetch($path);
}

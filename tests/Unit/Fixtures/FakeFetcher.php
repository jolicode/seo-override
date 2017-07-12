<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Unit\Fixtures;

use Joli\SeoOverride\Fetcher;

class FakeFetcher implements Fetcher
{
    /** @var string */
    private $requiredOption;

    /** @var string */
    private $facultativeOption;

    public function __construct(string $requiredOption, $facultativeOption = 'yolo')
    {
        $this->requiredOption = $requiredOption;
        $this->facultativeOption = $facultativeOption;
    }

    public function fetch(string $path, string $domainAlias = null)
    {
        return null;
    }
}

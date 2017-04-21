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

/**
 * This fetcher looks for overrides by including a PHP array.
 *
 * Internally it stores the array returned by the included file in an InMemoryFetcher.
 *
 * @see InMemoryFetcher for the structure of the array
 */
class PhpFetcher implements Fetcher
{
    /** @var string */
    private $includePath;

    /** @var bool */
    private $strict;

    /** @var InMemoryFetcher */
    private $inMemoryFetcher;

    public function __construct(string $includePath, bool $strict = false)
    {
        $this->includePath = $includePath;
        $this->strict = $strict;
    }

    public function fetch(string $path, string $domainAlias = null)
    {
        if (!$this->inMemoryFetcher) {
            $overrides = [];

            if (file_exists($this->includePath)) {
                $overrides = require $this->includePath;

                if (!is_array($overrides)) {
                    if ($this->strict) {
                        throw new \LogicException(sprintf('Included file "%s" should return an array', $this->includePath));
                    }
                    $overrides = [];
                }
            } elseif ($this->strict) {
                throw new \LogicException(sprintf('No file to include was found at "%s"', $this->includePath));
            }

            $this->inMemoryFetcher = new InMemoryFetcher($overrides);
        }

        return $this->inMemoryFetcher->fetch($path, $domainAlias);
    }
}

<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\Blacklister;

use Joli\SeoOverride\Bridge\Symfony\Blacklister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChainBlacklister implements Blacklister
{
    /**
     * @var Blacklister[]
     */
    private $blacklisters = [];

    /**
     * @param Blacklister[] $blacklisters
     */
    public function __construct(array $blacklisters)
    {
        $this->blacklisters = $blacklisters;
    }

    public function isBlacklisted(Request $request, Response $response): bool
    {
        foreach ($this->blacklisters as $blacklister) {
            if ($blacklister->isBlacklisted($request, $response)) {
                return true;
            }
        }

        return false;
    }
}

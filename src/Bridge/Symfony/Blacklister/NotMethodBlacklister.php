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

class NotMethodBlacklister implements Blacklister
{
    /** @var string[] */
    private $methods;

    /**
     * @param string|string[] $method
     */
    public function __construct($method)
    {
        $this->methods = (array) $method;
    }

    public function isBlacklisted(Request $request, Response $response): bool
    {
        foreach ($this->methods as $method) {
            if ($request->isMethod($method)) {
                return false;
            }
        }

        return true;
    }
}

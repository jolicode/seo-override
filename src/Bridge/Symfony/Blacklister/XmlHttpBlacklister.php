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

/**
 * Class XmlHttpBlacklister.
 */
class XmlHttpBlacklister implements Blacklister
{
    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return bool
     */
    public function isBlacklisted(Request $request, Response $response): bool
    {
        return $request->isXmlHttpRequest();
    }
}

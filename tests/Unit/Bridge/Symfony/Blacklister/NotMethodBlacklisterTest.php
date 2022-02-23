<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Unit\Bridge\Symfony\Blacklister;

use Joli\SeoOverride\Bridge\Symfony\Blacklister\NotMethodBlacklister;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotMethodBlacklisterTest extends TestCase
{
    /** @var NotMethodBlacklisterTest */
    private $blacklister;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testItBlacklistsNotAcceptedMethodRequests()
    {
        $request = new Request();
        $request->setMethod('put');

        $blacklister = new NotMethodBlacklister(['GET', 'POST']);

        self::assertTrue($blacklister->isBlacklisted($request, new Response()));
    }

    public function testItDoesNotBlacklistAcceptedMethodRequests()
    {
        $request = new Request();
        $request->setMethod('get');

        $blacklister = new NotMethodBlacklister('GET');

        self::assertFalse($blacklister->isBlacklisted($request, new Response()));

        $request = new Request();
        $request->setMethod('get');

        $blacklister = new NotMethodBlacklister(['GET', 'POST']);

        self::assertFalse($blacklister->isBlacklisted($request, new Response()));

        $request = new Request();
        $request->setMethod('POST');

        $blacklister = new NotMethodBlacklister(['GET', 'POST']);

        self::assertFalse($blacklister->isBlacklisted($request, new Response()));
    }
}

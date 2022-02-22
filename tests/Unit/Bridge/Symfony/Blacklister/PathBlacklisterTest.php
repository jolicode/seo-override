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

use Joli\SeoOverride\Bridge\Symfony\Blacklister\PathBlacklister;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PathBlacklisterTest extends TestCase
{
    /** @var PathBlacklister */
    private $blacklister;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blacklister = new PathBlacklister('^/(admin|account)');
    }

    public function testItBlacklistsMatchingRequests()
    {
        $response = new Response(200);

        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/admin',
        ]);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));

        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/admin/users/list',
        ]);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));

        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/account/',
        ]);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));
    }

    public function testItDoesNotBlacklistNotMatchingRequests()
    {
        $response = new Response(200);

        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/',
        ]);
        self::assertFalse($this->blacklister->isBlacklisted($request, $response));

        $request = new Request([], [], [], [], [], [
            'REQUEST_URI' => '/foo/admin',
        ]);
        self::assertFalse($this->blacklister->isBlacklisted($request, $response));
    }
}

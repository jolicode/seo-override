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

use Joli\SeoOverride\Bridge\Symfony\Blacklister\Not2xxBlacklister;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Not2xxBlacklisterTest extends TestCase
{
    /** @var Not2xxBlacklister */
    private $blacklister;

    public function setUp()
    {
        parent::setUp();

        $this->blacklister = new Not2xxBlacklister();
    }

    public function test_it_blacklists_non_2xx_responses()
    {
        $request = new Request();

        $response = new Response('', 100);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));

        $response = new Response('', 300);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));

        $response = new Response('', 404);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));

        $response = new Response('', 500);
        self::assertTrue($this->blacklister->isBlacklisted($request, $response));
    }

    public function test_it_does_not_blacklist_2xx_responses()
    {
        $request = new Request();

        $response = new Response('', 200);
        self::assertFalse($this->blacklister->isBlacklisted($request, $response));

        $response = new Response('', 201);
        self::assertFalse($this->blacklister->isBlacklisted($request, $response));
    }
}

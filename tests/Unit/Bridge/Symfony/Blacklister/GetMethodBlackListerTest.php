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

use Joli\SeoOverride\Bridge\Symfony\Blacklister\GetMethodBlacklister;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetMethodBlackListerTest extends TestCase
{
    /** @var GetMethodBlacklister */
    private $blacklister;

    public function setUp()
    {
        parent::setUp();

        $this->blacklister = new GetMethodBlacklister();
    }

    public function test_it_blacklists_get_method_requests()
    {
        $request = new Request();
        $request->setMethod('get');

        self::assertTrue($this->blacklister->isBlacklisted($request, new Response()));
    }

    public function test_it_does_not_blacklist_get_method_requests()
    {
        $request = new Request();
        $request->setMethod('post');

        self::assertFalse($this->blacklister->isBlacklisted($request, new Response()));
    }
}

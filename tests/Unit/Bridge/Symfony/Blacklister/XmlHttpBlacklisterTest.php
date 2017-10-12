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

use Joli\SeoOverride\Bridge\Symfony\Blacklister\XmlHttpBlacklister;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class XmlHttpBlacklisterTest extends TestCase
{
    /** @var XmlHttpBlacklisterTest */
    private $blacklister;

    public function setUp()
    {
        parent::setUp();

        $this->blacklister = new XmlHttpBlacklister();
    }

    public function test_it_blacklists_xml_http_requests()
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        self::assertTrue($this->blacklister->isBlacklisted($request, new Response()));
    }

    public function test_it_does_not_blacklist_xml_http_requests()
    {
        $request = new Request();

        self::assertFalse($this->blacklister->isBlacklisted($request, new Response()));
    }
}

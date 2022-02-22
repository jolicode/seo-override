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

    protected function setUp()
    {
        parent::setUp();

        $this->blacklister = new XmlHttpBlacklister();
    }

    public function testItBlacklistsXmlHttpRequests()
    {
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        self::assertTrue($this->blacklister->isBlacklisted($request, new Response()));
    }

    public function testItDoesNotBlacklistNotXmlHttpRequests()
    {
        $request = new Request();

        self::assertFalse($this->blacklister->isBlacklisted($request, new Response()));
    }
}

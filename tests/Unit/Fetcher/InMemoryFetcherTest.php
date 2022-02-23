<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Tests\Unit\Fetcher;

use Joli\SeoOverride\Fetcher\InMemoryFetcher;
use PHPUnit\Framework\TestCase;

class InMemoryFetcherTest extends TestCase
{
    public function testItReturnsNullWhenNoOverridesGiven()
    {
        $fetcher = new InMemoryFetcher([]);

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function testItReturnsNullWhenNoneOverrideForDomains()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => null,
            '' => null,
        ]);

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
        self::assertNull($fetcher->fetch('/url1', 'domain_alias_2'));
        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function testItReturnsNullWithNoMatchingOverrides()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => [],
            'domain_alias_2' => [],
            '' => [],
        ]);

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
        self::assertNull($fetcher->fetch('/url1', 'domain_alias_2'));
        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function testItReturnsNullWithInvalidMatchingOverrides()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => [
                '/url1' => null,
            ],
            'domain_alias_2' => [
                '/url1' => null,
            ],
            '' => [
                '/url1' => null,
            ],
        ]);

        self::assertNull($fetcher->fetch('/url1', 'domain_alias_1'));
        self::assertNull($fetcher->fetch('/url1', 'domain_alias_2'));
        self::assertNull($fetcher->fetch('/url1', null));
    }

    public function testItReturnsSeoWithMatchingOverrides()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => [
                '/url1' => [],
            ],
            'domain_alias_2' => [
                '/url1' => [],
            ],
            '' => [
                '/url1' => [],
            ],
        ]);

        $seo1 = $fetcher->fetch('/url1', 'domain_alias_1');

        self::assertNotNull($seo1);
        self::assertNull($seo1->getTitle());

        $seo2 = $fetcher->fetch('/url1', 'domain_alias_2');

        self::assertNotNull($seo2);
        self::assertNull($seo2->getTitle());

        $seo3 = $fetcher->fetch('/url1', null);

        self::assertNotNull($seo3);
        self::assertNull($seo3->getTitle());
    }

    public function testItReturnsSeoWithMatchingOverridesFromRightDomain()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => [
                '/url1' => [
                    'title' => 'title 1',
                ],
            ],
            '' => [
                '/url1' => [
                    'title' => 'title 2',
                ],
            ],
        ]);

        $seo1 = $fetcher->fetch('/url1', 'domain_alias_1');

        self::assertNotNull($seo1);
        self::assertSame('title 1', $seo1->getTitle());

        $seo2 = $fetcher->fetch('/url1', null);

        self::assertNotNull($seo2);
        self::assertSame('title 2', $seo2->getTitle());
    }

    public function testItReturnsCorrectlyHydratedSeoForMatchingOverrides()
    {
        $fetcher = new InMemoryFetcher([
            'domain_alias_1' => [
                '/url1' => [
                    'title' => 'title 1',
                    'description' => 'description 1',
                    'keywords' => 'keywords 1',
                    'robots' => 'robots 1',
                    'canonical' => '/canonical-1',
                    'ogTitle' => 'og:title 1',
                    'ogDescription' => 'og:description 1',
                ],
                '/url2' => [
                    'title' => 'title 2',
                ],
            ],
        ]);

        $seo1 = $fetcher->fetch('/url1', 'domain_alias_1');

        self::assertNotNull($seo1);
        self::assertSame('title 1', $seo1->getTitle());
        self::assertSame('description 1', $seo1->getDescription());
        self::assertSame('keywords 1', $seo1->getKeywords());
        self::assertSame('robots 1', $seo1->getRobots());
        self::assertSame('/canonical-1', $seo1->getCanonical());
        self::assertSame('og:title 1', $seo1->getOgTitle());
        self::assertSame('og:description 1', $seo1->getOgDescription());

        $seo2 = $fetcher->fetch('/url2', 'domain_alias_1');

        self::assertNotNull($seo2);
        self::assertSame('title 2', $seo2->getTitle());
        self::assertNull($seo2->getDescription());
        self::assertNull($seo2->getKeywords());
        self::assertNull($seo2->getRobots());
        self::assertNull($seo2->getCanonical());
        self::assertNull($seo2->getOgTitle());
        self::assertNull($seo2->getOgDescription());
    }

    public function testItHandlesCatchAllDomain()
    {
        $fetcher = new InMemoryFetcher([
            '' => [
                '/url1' => [
                    'title' => 'title 1',
                ],
            ],
        ]);

        $seo = $fetcher->fetch('/url1', null);

        self::assertNotNull($seo);
        self::assertSame('title 1', $seo->getTitle());
    }
}

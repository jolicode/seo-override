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

use Joli\SeoOverride\Fetcher\PhpFetcher;
use PHPUnit\Framework\TestCase;

class PhpFetcherTest extends TestCase
{
    public function testItReturnsNullWhenNotExistingFileGivenInNonStrictMode()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/not_existing_file.php', false);

        self::assertNull($fetcher->fetch('/url', 'domain_alias_1'));
    }

    public function testItThrowsExceptionWhenNotExistingFileGivenInStrictMode()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/not_existing_file.php', true);

        try {
            $fetcher->fetch('/url', 'domain_alias_1');
            self::fail('An exception should have been thrown');
        } catch (\LogicException $e) {
            self::assertSame('No file to include was found at "' . __DIR__ . '/../Fixtures/PhpFetcher/not_existing_file.php"', $e->getMessage());
        }
    }

    public function testItReturnsNullWhenIncludedFileDoesNotReturnAnArrayInNonStrictMode()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/no_array_returned.php', false);

        self::assertNull($fetcher->fetch('/url', 'domain_alias_1'));
    }

    public function testItThrowsExceptionWhenIncludedFileDoesNotReturnAnArrayInStrictMode()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/no_array_returned.php', true);

        try {
            $fetcher->fetch('/url', 'domain_alias_1');
            self::fail('An exception should have been thrown');
        } catch (\LogicException $e) {
            self::assertSame('Included file "' . __DIR__ . '/../Fixtures/PhpFetcher/no_array_returned.php" should return an array', $e->getMessage());
        }
    }

    public function testItReturnsNullWhenIncludedFileReturnAnEmptyArray()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/empty_array.php', false);

        self::assertNull($fetcher->fetch('/url', 'domain_alias_1'));
    }

    public function testItReturnsSeoWhenIncludedFileContainsMatchingOverrides()
    {
        $fetcher = new PhpFetcher(__DIR__ . '/../Fixtures/PhpFetcher/sample.php', false);

        $seo1 = $fetcher->fetch('/url1', 'domain_alias_1');

        self::assertNotNull($seo1);
        self::assertSame('title 1', $seo1->getTitle());

        $seo2 = $fetcher->fetch('/url2', null);

        self::assertNotNull($seo2);
        self::assertSame('title 2', $seo2->getTitle());
    }
}

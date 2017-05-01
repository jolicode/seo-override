<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\tests\Unit;

use Joli\SeoOverride\Fetcher;
use Joli\SeoOverride\Seo;
use Joli\SeoOverride\SeoManager;
use PHPUnit\Framework\TestCase;

class SeoManagerTest extends TestCase
{
    public function test_it_updates_full_seo_from_fetcher()
    {
        $seo = new Seo();
        $seo->setTitle('new title');
        $seo->setDescription('new description');
        $seo->setKeywords('new keywords');
        $seo->setRobots('new robots');
        $seo->setCanonical('/new-canonical');
        $seo->setOgTitle('new og:title');
        $seo->setOgDescription('new og:description');

        $fetcher1 = $this->prophesize(Fetcher::class);
        $fetcher1->fetch('/', null)->willReturn(null);

        $fetcher2 = $this->prophesize(Fetcher::class);
        $fetcher2->fetch('/', null)->willReturn($seo);

        $seoManager = new SeoManager([
            $fetcher1->reveal(),
            $fetcher2->reveal(),
        ], []);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('new title', $seoManager->getSeo()->getTitle());
        self::assertSame('new description', $seoManager->getSeo()->getDescription());
        self::assertSame('new keywords', $seoManager->getSeo()->getKeywords());
        self::assertSame('new robots', $seoManager->getSeo()->getRobots());
        self::assertSame('/new-canonical', $seoManager->getSeo()->getCanonical());
        self::assertSame('new og:title', $seoManager->getSeo()->getOgTitle());
        self::assertSame('new og:description', $seoManager->getSeo()->getOgDescription());
    }

    public function test_it_updates_partial_seo()
    {
        $seo1 = new Seo();
        $seo1->setTitle('title1');
        $seo1->setDescription('description1');
        $seo1->setKeywords('keywords1');
        $seo1->setRobots('robots1');
        $seo1->setCanonical('/canonical1');
        $seo1->setOgTitle('og:title1');
        $seo1->setOgDescription('og:description1');

        $seo2 = new Seo();
        $seo2->setTitle('title2');
        $seo2->setDescription('description2');
        $seo2->setKeywords('keywords2');

        $fetcher = $this->prophesize(Fetcher::class);
        $fetcher->fetch('/', null)->willReturn($seo2);

        $seoManager = new SeoManager([
            $fetcher->reveal(),
        ], [], $seo1);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('title2', $seoManager->getSeo()->getTitle());
        self::assertSame('description2', $seoManager->getSeo()->getDescription());
        self::assertSame('keywords2', $seoManager->getSeo()->getKeywords());
        self::assertSame('robots1', $seoManager->getSeo()->getRobots());
        self::assertSame('/canonical1', $seoManager->getSeo()->getCanonical());
        self::assertSame('og:title1', $seoManager->getSeo()->getOgTitle());
        self::assertSame('og:description1', $seoManager->getSeo()->getOgDescription());
    }

    public function test_it_respects_priority_in_fetcher()
    {
        $seo1 = new Seo();
        $seo1->setTitle('title1');

        $seo2 = new Seo();
        $seo2->setTitle('title2');

        $fetcher1 = $this->prophesize(Fetcher::class);
        $fetcher1->fetch('/', null)->willReturn($seo1);

        $fetcher2 = $this->prophesize(Fetcher::class);
        $fetcher2->fetch('/', null)->willReturn($seo2);

        $seoManager = new SeoManager([
            $fetcher1->reveal(),
            $fetcher2->reveal(),
        ], []);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('title1', $seoManager->getSeo()->getTitle());
    }

    public function test_it_determines_domain_alias()
    {
        $seo = new Seo();
        $seo->setTitle('title');

        $fetcher = $this->prophesize(Fetcher::class);
        $fetcher->fetch('/', 'domain2')->willReturn($seo);

        $seoManager = new SeoManager([
            $fetcher->reveal(),
        ], [
            'domain1' => 'example.fr',
            'domain2' => 'example.com',
        ]);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('title', $seoManager->getSeo()->getTitle());
    }

    public function test_it_respects_priority_in_domain_alias()
    {
        $seo = new Seo();
        $seo->setTitle('title');

        $fetcher = $this->prophesize(Fetcher::class);
        $fetcher->fetch('/', 'domain1')->willReturn($seo);

        $seoManager = new SeoManager([
            $fetcher->reveal(),
        ], [
            'domain1' => 'example.com',
            'domain2' => 'example.com',
        ]);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('title', $seoManager->getSeo()->getTitle());
    }

    public function test_it_looks_for_catch_all_domain_when_no_override_found()
    {
        $seo = new Seo();
        $seo->setTitle('title');

        $fetcher = $this->prophesize(Fetcher::class);
        $fetcher->fetch('/', 'domain1')->willReturn(null);
        $fetcher->fetch('/', null)->willReturn($seo);

        $seoManager = new SeoManager([
            $fetcher->reveal(),
        ], [
            'domain1' => 'example.com',
        ]);

        $seoManager->updateSeo('/', 'www.example.com');

        self::assertSame('title', $seoManager->getSeo()->getTitle());
    }

    public function test_it_overrides_html()
    {
        $seo = new Seo();
        $seo->setTitle('new title');
        $seo->setDescription('new description');
        $seo->setKeywords('new keywords');
        $seo->setRobots('new robots');
        $seo->setCanonical('/new-canonical');
        $seo->setOgTitle('new og:title');
        $seo->setOgDescription('new og:description');

        $seoManager = new SeoManager([], [], $seo);

        $html = <<<'HTML'
<html>
<head>
<!--SEO_TITLE--><title>old title</title><!--/SEO_TITLE-->
<!--SEO_DESCRIPTION--><meta name="description" content="old description"><!--/SEO_DESCRIPTION-->
<!--SEO_KEYWORDS--><meta name="keywords" content="old keywords"><!--/SEO_KEYWORDS-->
<!--SEO_ROBOTS--><meta name="robots" content="old robots"><!--/SEO_ROBOTS-->
<!--SEO_CANONICAL--><link rel="canonical" href="/old-canonical"><!--/SEO_CANONICAL-->
<!--SEO_OG_TITLE--><meta property="og:title" content="old og:title"><!--/SEO_OG_TITLE-->
<!--SEO_OG_DESCRIPTION--><meta property="og:description" content="old og:description"><!--/SEO_OG_DESCRIPTION-->
</head>
<body></body>
</html>
HTML;
        $expected = <<<'HTML'
<html>
<head>
<title>new title</title>
<meta name="description" content="new description" />
<meta name="keywords" content="new keywords" />
<meta name="robots" content="new robots" />
<link rel="canonical" href="/new-canonical" />
<meta property="og:title" content="new og:title" />
<meta property="og:description" content="new og:description" />
</head>
<body></body>
</html>
HTML;

        self::assertSame($expected, $seoManager->overrideHtml($html));
    }

    public function test_it_does_not_override_html_when_no_override()
    {
        $seoManager = new SeoManager([], []);

        $html = <<<'HTML'
<html>
<head>
<title>old title</title>
<meta name="description" content="old description">
<meta name="keywords" content="old keywords">
<meta name="robots" content="old robots">
<link rel="canonical" href="/old-canonical">
<meta property="og:title" content="old og:title">
<meta property="og:description" content="old og:description">
</head>
<body></body>
</html>
HTML;

        self::assertSame($html, $seoManager->overrideHtml($html));
    }

    public function test_it_updates_seo_and_overrides_html()
    {
        $seo = new Seo();
        $seo->setTitle('new title');

        $fetcher = $this->prophesize(Fetcher::class);
        $fetcher->fetch('/', 'domain2')->willReturn($seo);

        $seoManager = new SeoManager([
            $fetcher->reveal(),
        ], [
            'domain1' => 'example.fr',
            'domain2' => 'example.com',
        ]);

        $html = <<<'HTML'
<html>
<head>
<!--SEO_TITLE--><title>old title</title><!--/SEO_TITLE-->
</head>
<body></body>
</html>
HTML;
        $expected = <<<'HTML'
<html>
<head>
<title>new title</title>
</head>
<body></body>
</html>
HTML;

        self::assertSame($expected, $seoManager->updateAndOverride($html, '/', 'www.example.com'));
    }
}

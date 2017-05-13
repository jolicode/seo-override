<?php

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Seo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;

class SeoManager extends \Joli\SeoOverride\SeoManager // @todo should use an interface
{
    protected $seoManager;

    public function __construct(\Joli\SeoOverride\SeoManager $seoManager)
    {
        $this->seoManager = $seoManager;
    }

    public function getSeo(): Seo
    {
        return $this->seoManager->getSeo();
    }

    public function updateAndOverride(string $html, string $path, string $domain): string
    {
        return $this->seoManager->updateAndOverride($html, $path, $domain);
    }

    public function updateSeo(string $path, string $domain): Seo
    {
        // @todo HERE, store all the stuffs!
        return $this->seoManager->updateSeo($path, $domain);
    }

    public function overrideHtml(string $html): string
    {
        // @todo HERE, store!

        return $this->seoManager->overrideHtml($html);
    }
}

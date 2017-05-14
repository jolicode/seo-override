<?php

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Fetcher;
use Joli\SeoOverride\Seo;
use Joli\SeoOverride\SeoManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;

class SeoManager extends \Joli\SeoOverride\SeoManager
{
    protected $data = [];

    public function __construct(array $fetchers, array $domains, Seo $seo = null)
    {
        parent::__construct($fetchers, $domains, $seo);

        $this->data['seo_versions'][] = ['seo' => $seo]; // @todo clone?
        $this->data['fetchers'] = [];
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAndOverride(string $html, string $path, string $domain): string
    {
        $this->data['path'] = $path;
        $this->data['domain'] = $domain;
        $this->data['status'] = SeoOverrideDataCollector::STATUS_DEFAULT;

        return parent::updateAndOverride($html, $path, $domain);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Fetcher $fetcher, string $path, $domainAlias)
    {
        $seo = parent::fetch($fetcher, $path, $domainAlias);

        $this->data['fetchers'][] = [
            'name' => get_class($fetcher),
            'matched' => $seo instanceof Seo,
        ];

        if ($seo) {
            $this->data['status'] = SeoOverrideDataCollector::STATUS_MATCHED;
            $this->data['seo_versions'][] = [
                'seo' => $seo,
                'fetcher' => get_class($fetcher),
            ]; // @todo clone?
        }

        return $seo;
    }
}

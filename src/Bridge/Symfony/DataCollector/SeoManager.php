<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Fetcher;
use Joli\SeoOverride\Seo;
use Joli\SeoOverride\SeoManager as BaseSeoManager;

class SeoManager extends BaseSeoManager
{
    protected $data = [];
    private $fetchersMapping;

    public function __construct(array $fetchers, array $domains, Seo $seo = null, array $fetchersMapping)
    {
        parent::__construct($fetchers, $domains, $seo);

        $this->fetchersMapping = $fetchersMapping;

        $this->data['fetchersMapping'] = $fetchersMapping;
        $this->data['fetchers'] = [];
        $this->data['domains'] = array_keys($domains);
        $this->data['status'] = SeoOverrideDataCollector::STATUS_NOT_RUN;
        $this->data['seo_versions'] = [];

        if ($seo) {
            $this->data['seo_versions'][] = [
                'seo' => clone $seo,
                'origin' => 'initial',
            ];
        }
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSeo(string $path, string $domain): Seo
    {
        $this->data['path'] = $path;
        $this->data['domain'] = $domain;
        $this->data['status'] = SeoOverrideDataCollector::STATUS_NOT_MATCHED;

        $this->data['seo_versions'][] = [
            'seo' => clone $this->getSeo(),
            'origin' => 'before update',
        ];

        return parent::updateSeo($path, $domain);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Fetcher $fetcher, string $path, string $domainAlias = null)
    {
        $callback = function (string $path, string $domainAlias = null, Seo $seo = null) use ($fetcher) {
            $this->data['fetchers'][] = [
                'type' => array_search(\get_class($fetcher), $this->fetchersMapping, true),
                'class' => \get_class($fetcher),
                'matched' => $seo instanceof Seo,
                'domain_alias' => $domainAlias,
            ];
        };
        $callback->bindTo($this);

        $collectorFetcher = new CallbackFetcher($fetcher, $callback);
        $seo = parent::fetch($collectorFetcher, $path, $domainAlias);

        if ($seo) {
            $this->data['status'] = SeoOverrideDataCollector::STATUS_MATCHED;
            $this->data['seo_versions'][] = [
                'seo' => clone $seo,
                'fetcher_type' => array_search(\get_class($fetcher), $this->fetchersMapping, true),
                'fetcher_class' => \get_class($fetcher),
                'origin' => 'from fetcher',
            ];
        }

        return $seo;
    }

    /**
     * {@inheritdoc}
     */
    public function overrideHtml(string $html): string
    {
        if (SeoOverrideDataCollector::STATUS_NOT_RUN === $this->data['status']) {
            $this->data['status'] = SeoOverrideDataCollector::STATUS_BLACKLISTED;
        }

        return parent::overrideHtml($html);
    }

    /**
     * {@inheritdoc}
     */
    protected function findDomainAlias(string $domain)
    {
        $domainAlias = parent::findDomainAlias($domain);
        $this->data['domain_alias'] = $domainAlias;

        return $domainAlias;
    }
}

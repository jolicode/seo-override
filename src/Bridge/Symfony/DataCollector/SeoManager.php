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

use Joli\SeoOverride\AbstractSeoManager;
use Joli\SeoOverride\Fetcher;
use Joli\SeoOverride\Seo;
use Joli\SeoOverride\SeoManagerInterface;

class SeoManager extends AbstractSeoManager implements SeoManagerInterface
{
    protected $baseSeoManager;
    protected $data = [];
    private $fetchersMapping;

    public function __construct(SeoManagerInterface $seoManager, array $fetchersMapping)
    {
        $this->baseSeoManager = $seoManager;
        $this->fetchersMapping = $fetchersMapping;

        $this->data['fetchersMapping'] = $fetchersMapping;
        $this->data['fetchers'] = [];
        $this->data['domains'] = array_keys($this->baseSeoManager->getDomains());
        $this->data['status'] = SeoOverrideDataCollector::STATUS_NOT_RUN;
        $this->data['seo_versions'] = [];

        if ($this->baseSeoManager->getSeo()) {
            $this->data['seo_versions'][] = [
                'seo' => clone $this->baseSeoManager->getSeo(),
                'origin' => 'initial',
            ];
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function getSeo(): Seo
    {
        return $this->baseSeoManager->getSeo();
    }

    public function getDomains(): array
    {
        return $this->baseSeoManager->getDomains();
    }

    public function getEncoding(): string
    {
        return $this->baseSeoManager->getEncoding();
    }

    public function getFetchers(): array
    {
        return $this->baseSeoManager->getFetchers();
    }

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
        $seo = $this->baseSeoManager->fetch($collectorFetcher, $path, $domainAlias);

        if ($seo) {
            $this->data['status'] = SeoOverrideDataCollector::STATUS_MATCHED;
            $this->data['seo_versions'][] = [
                'seo' => clone $seo,
                'fetcher_type' => array_search(\get_class($fetcher), $this->fetchersMapping, true),
                'fetcher_class' => \get_class($fetcher),
                'origin' => 'from fetcher',
            ];
        }

        return parent::fetch($fetcher, $path, $domainAlias);
    }

    public function overrideHtml(string $html): string
    {
        if (SeoOverrideDataCollector::STATUS_NOT_RUN === $this->data['status']) {
            $this->data['status'] = SeoOverrideDataCollector::STATUS_BLACKLISTED;
        }

        return $this->baseSeoManager->overrideHtml($html);
    }

    protected function findDomainAlias(string $domain)
    {
        return $this->data['domain_alias'] = $this->baseSeoManager->findDomainAlias($domain);
    }
}

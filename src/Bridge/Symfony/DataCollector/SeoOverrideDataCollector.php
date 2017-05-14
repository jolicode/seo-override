<?php

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Seo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;

class SeoOverrideDataCollector extends DataCollector implements LateDataCollectorInterface
{
    const STATUS_DEFAULT = 'default';
    const STATUS_MATCHED = 'matched';

    /**
     * @var SeoManager
     */
    private $seoManager;

    public function __construct(SeoManager $seoManager)
    {
        $this->seoManager = $seoManager;
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $this->data = $this->seoManager->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    public function getFetchers()
    {
        return $this->data['fetchers'];
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function getVersions()
    {
        return $this->data['seo_versions'];
    }

    public function getPath()
    {
        return $this->data['path'];
    }

    public function getDomain()
    {
        return $this->data['domain'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'seo-override';
    }
}

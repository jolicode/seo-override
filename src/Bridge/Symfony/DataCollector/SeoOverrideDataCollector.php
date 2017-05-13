<?php

namespace Joli\SeoOverride\Bridge\Symfony\DataCollector;

use Joli\SeoOverride\Seo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;

class SeoOverrideDataCollector extends DataCollector
{
    const STATUS_DEFAULT = 'default';
    const STATUS_MATCHED = 'matched';

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $default = new Seo();
        $default->setTitle('My default title');

        $override = clone $default;
        $override->setTitle('Overrided title');
        $override->setRobots('noindex');

        $this->data = array(
            'status' => self::STATUS_MATCHED,
            'path' => $request->getPathInfo(),
            'default' => $default,
            'override' => $override,
        );
    }

    public function getDefault()
    {
        return $this->data['default'];
    }

    public function getOverride()
    {
        return $this->data['override'];
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function getPath()
    {
        return $this->data['path'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'seo-override';
    }
}

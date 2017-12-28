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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

class SeoOverrideDataCollector extends DataCollector implements LateDataCollectorInterface
{
    const STATUS_NOT_RUN = 'not run';
    const STATUS_BLACKLISTED = 'blacklisted';
    const STATUS_NOT_MATCHED = 'not_match';
    const STATUS_MATCHED = 'matched';

    const STATUS_LABELS = [
        self::STATUS_NOT_RUN => 'not run',
        self::STATUS_BLACKLISTED => 'blacklisted',
        self::STATUS_NOT_MATCHED => 'not matched',
        self::STATUS_MATCHED => 'matched',
    ];

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

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = array();
    }

    public function getFetchers()
    {
        return $this->data['fetchers'];
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function getStatusLabel()
    {
        return self::STATUS_LABELS[$this->data['status']];
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

    public function getDomains()
    {
        return $this->data['domains'];
    }

    public function getDomainAlias()
    {
        return $this->data['domain_alias'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'seo-override';
    }
}

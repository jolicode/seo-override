<?php

namespace Joli\SeoOverride\tests\Functional\Fixtures\symfony\app;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Joli\SeoOverride\Bridge\Symfony\SeoOverrideBundle(),
        ];

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        if (version_compare(self::VERSION, '3.0.0', '<')) {
            $loader->load($this->getRootDir().'/config/config_lower_than_3-0.yml');
        } else {
            $loader->load($this->getRootDir().'/config/config_greater_than_3-0.yml');
        }
    }
}

<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SeoOverrideExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->registerFetchersConfiguration($config['fetchers'], $container, $loader);
        $this->registerDomainsConfiguration($config['domains'], $container);
        $this->registerEncodingConfiguration($config['encoding'] ?? null, $container);

        if ($container->hasParameter('kernel.debug') && $container->getParameter('kernel.debug')) {
            $loader->load('debug.yml');
        }
    }

    private function registerFetchersConfiguration(array $fetchers, ContainerBuilder $container, YamlFileLoader $loader)
    {
        // This parameter will only be used in the compiler pass
        $container->setParameter('seo_override.fetchers_configuration', $fetchers);

        $types = array_column($fetchers, 'type');

        if (in_array('doctrine', $types, true)) {
            $loader->load('doctrine_fetcher.yml');
        }
    }

    private function registerDomainsConfiguration(array $domains, ContainerBuilder $container)
    {
        $container->setParameter('seo_override.domains', $domains);
    }

    private function registerEncodingConfiguration(string $encoding = null, ContainerBuilder $container)
    {
        if (!$encoding) {
            return;
        }

        $container
            ->getDefinition('seo_override.manager')
            ->addMethodCall('setEncoding', [$encoding])
        ;
    }
}

<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterFetcherPass extends AbstractRegisterServicePass
{
    protected function getTag(): string
    {
        return 'seo_override.fetcher';
    }

    protected function getName(bool $plural): string
    {
        return $plural ? 'fetchers' : 'fetcher';
    }

    protected function getConfigurationParameterName(): string
    {
        return 'seo_override.fetchers_configuration';
    }

    protected function processServiceList(ContainerBuilder $container, array $serviceDefinitionsByType)
    {
        if ($container->hasParameter('kernel.debug') && $container->getParameter('kernel.debug')) {
            $container->setParameter('seo_override.fetchers_mapping', array_map(function (Definition $definition) {
                return $definition->getClass();
            }, $serviceDefinitionsByType));
        }

        $definition = $container->getDefinition('seo_override.manager');
        $definition->replaceArgument(0, array_values($serviceDefinitionsByType));
    }
}

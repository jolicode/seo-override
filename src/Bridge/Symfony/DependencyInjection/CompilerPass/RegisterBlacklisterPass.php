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

class RegisterBlacklisterPass extends AbstractRegisterServicePass
{
    protected function getTag(): string
    {
        return 'seo_override.blacklister';
    }

    protected function getName(bool $plurial): string
    {
        return $plurial ? 'blacklisters' : 'blacklister';
    }

    protected function getConfigurationParameterName(): string
    {
        return 'seo_override.blacklisters_configuration';
    }

    protected function processServiceList(ContainerBuilder $container, array $serviceDefinitionsByType)
    {
        if ($container->hasParameter('kernel.debug') && $container->getParameter('kernel.debug')) {
            $container->setParameter('seo_override.blacklisters_mapping', array_map(function (Definition $definition) {
                return $definition->getClass();
            }, $serviceDefinitionsByType));
        }

        if (empty($serviceDefinitionsByType)) {
            $alias = 'seo_override.blacklister.null';
        } else {
            $definition = $container->getDefinition('seo_override.blacklister.chain');
            $definition->replaceArgument(0, array_values($serviceDefinitionsByType));

            $alias = 'seo_override.blacklister.chain';
        }

        $container->setAlias('seo_override.blacklister.default', $alias);
    }
}

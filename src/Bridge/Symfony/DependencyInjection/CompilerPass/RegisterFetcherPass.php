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

use Joli\SeoOverride\Bridge\Symfony\DataCollector\SeoManager;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class RegisterFetcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $fetcherByAlias = $this->assertTagsDefineUniqueAliases(
            $container->findTaggedServiceIds('seo_override.fetcher')
        );

        $fetcherDefinitions = [];
        $fetchersConfiguration = $container->getParameter('seo_override.fetchers_configuration');

        foreach ($fetchersConfiguration as $fetcherConfiguration) {
            $type = $fetcherConfiguration['type'];
            unset($fetcherConfiguration['type']);

            if (!array_key_exists($type, $fetcherByAlias)) {
                throw new LogicException(sprintf(
                    'Unkown "%s" fetcher. Available fetchers are: %s',
                    $type,
                    implode(',', array_keys($fetcherByAlias))
                ));
            }

            $this->assertFetcherHasMandatoryOption(
                $fetcherConfiguration,
                $fetcherByAlias[$type]['attributes'],
                $type
            );

            $fetcherDefinition = $container->getDefinition($fetcherByAlias[$type]['id']);
            $arguments = $this->getContructorArguments($fetcherDefinition);

            foreach ($fetcherConfiguration as $option => $value) {
                $optionNormalized = $this->camelize($option);
                $index = array_search($optionNormalized, $arguments, true);

                if ($index === false) {
                    throw new LogicException(sprintf(
                        'Unkown "%s" option for fetcher "%s"',
                        $option,
                        $type
                    ));
                }

                $fetcherDefinition->replaceArgument($index, $value);
            }

            $fetcherDefinitions[] = $fetcherDefinition;
        }

        $definition = $container->getDefinition('seo_override.manager');
        $definition->replaceArgument(0, $fetcherDefinitions);

        if (true) { // @todo
            $definition->setClass(SeoManager::class);
        }
    }

    private function assertTagsDefineUniqueAliases(array $availableFetcherIds): array
    {
        $fetchersByAlias = [];
        $fetcherIdsMissingAlias = [];

        foreach ($availableFetcherIds as $fetcherId => $tags) {
            foreach ($tags as $attributes) {
                if (!array_key_exists('alias', $attributes)) {
                    $fetcherIdsMissingAlias[] = $fetcherId;
                    continue;
                }

                $fetchersByAlias[$attributes['alias']][] = [
                    'id' => $fetcherId,
                    'attributes' => $attributes,
                ];
            }
        }

        if (count($fetcherIdsMissingAlias) > 0) {
            throw new LogicException(sprintf(
                'These fetcher services do not define an "alias" attribute on their "seo_override.fetcher" tags: %s',
                implode(', ', array_unique($fetcherIdsMissingAlias))
            ));
        }

        $fetcherByAlias = [];

        foreach ($fetchersByAlias as $alias => $fetchers) {
            if (count($fetchers) > 1) {
                throw new LogicException(sprintf(
                    'Fetcher aliases should be unique. The "%s" alias is defined by these fetchers: %s',
                    $alias,
                    implode(', ', array_column($fetchers, 'id'))
                ));
            }

            $fetcherByAlias[$alias] = $fetchers[0];
        }

        return $fetcherByAlias;
    }

    private function assertFetcherHasMandatoryOption(array $fetcherConfiguration, array $tagAttributes, string $type)
    {
        if (array_key_exists('required_options', $tagAttributes)) {
            $requiredOptions = explode(',', $tagAttributes['required_options']);
            foreach ($requiredOptions as $option) {
                if (!array_key_exists($option, $fetcherConfiguration)) {
                    throw new LogicException(sprintf(
                        'The "%s" option is mandatory for fetcher "%s"',
                        $option,
                        $type
                    ));
                }
            }
        }
    }

    private function getContructorArguments(Definition $definition): array
    {
        $argumentsOrderByName = [];
        $class = new ReflectionClass($definition->getClass());
        $parameters = $class->getConstructor()->getParameters();

        foreach ($parameters as $index => $parameter) {
            $argumentsOrderByName[$index] = $parameter->getName();
        }

        return $argumentsOrderByName;
    }

    private function camelize(string $input, string $separator = '_'): string
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }
}

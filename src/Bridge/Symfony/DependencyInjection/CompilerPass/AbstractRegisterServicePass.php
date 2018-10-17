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

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;

abstract class AbstractRegisterServicePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceByAlias = $this->assertTagsDefineUniqueAliases(
            $container->findTaggedServiceIds($this->getTag())
        );

        $serviceDefinitionsByType = [];
        $servicesConfiguration = $container->getParameter($this->getConfigurationParameterName());

        foreach ($servicesConfiguration as $serviceConfiguration) {
            // Retrieve and remove the type from the configuration
            $type = $serviceConfiguration['type'];
            unset($serviceConfiguration['type']);

            // Check if the configured type correspond to an existing service
            if (!array_key_exists($type, $serviceByAlias)) {
                throw new LogicException(sprintf(
                    'Unknown "%s" %s. Available %s are: %s',
                    $type,
                    $this->getName(false),
                    $this->getName(true),
                    implode(',', array_keys($serviceByAlias))
                ));
            }

            // Check service will receive mandatory options
            $this->assertServiceHasMandatoryOption(
                $serviceConfiguration,
                $serviceByAlias[$type]['attributes'],
                $type
            );

            $serviceDefinition = $container->getDefinition($serviceByAlias[$type]['id']);
            $arguments = $this->getConstructorArguments($serviceDefinition);

            // Configure service constructor's argument
            foreach ($serviceConfiguration as $option => $value) {
                $optionNormalized = $this->camelize($option);

                $index = array_search($optionNormalized, $arguments, true);

                // Ensure the option matches a constructor's argument
                if (false === $index) {
                    throw new LogicException(sprintf(
                        'Unknown "%s" option for %s "%s"',
                        $option,
                        $this->getName(false),
                        $type
                    ));
                }

                $serviceDefinition->replaceArgument($index, $value);
            }

            $serviceDefinitionsByType[$type] = $serviceDefinition;
        }

        $this->processServiceList($container, $serviceDefinitionsByType);

        // Remove now useless parameter
        $container->getParameterBag()->remove($this->getConfigurationParameterName());
    }

    abstract protected function getTag(): string;

    abstract protected function getName(bool $plural): string;

    abstract protected function getConfigurationParameterName(): string;

    abstract protected function processServiceList(ContainerBuilder $container, array $serviceDefinitionsByType);

    private function assertTagsDefineUniqueAliases(array $availableServiceIds): array
    {
        $servicesByAlias = [];
        $serviceIdsMissingAlias = [];

        // Iterate on all services and retrieve their alias attribute (or not if missing)
        foreach ($availableServiceIds as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                // Missing alias attribute
                if (!array_key_exists('alias', $attributes)) {
                    $serviceIdsMissingAlias[] = $serviceId;
                    continue;
                }

                // Stock alias attribute
                $servicesByAlias[$attributes['alias']][] = [
                    'id' => $serviceId,
                    'attributes' => $attributes,
                ];
            }
        }

        // Error here if at least one service is missing an alias
        if (\count($serviceIdsMissingAlias) > 0) {
            throw new LogicException(sprintf(
                'These %s services do not define an "alias" attribute on their "%s" tags: %s',
                $this->getName(false),
                $this->getTag(),
                implode(', ', array_unique($serviceIdsMissingAlias))
            ));
        }

        // Iterate over services list, ensure aliases are unique and flatten the service list
        $serviceByAlias = [];
        foreach ($servicesByAlias as $alias => $services) {
            if (\count($services) > 1) {
                throw new LogicException(sprintf(
                    '%s aliases should be unique. The "%s" alias is defined by these %s: %s',
                    ucfirst($this->getName(false)),
                    $alias,
                    $this->getName(true),
                    implode(', ', array_column($services, 'id'))
                ));
            }

            $serviceByAlias[$alias] = $services[0];
        }

        return $serviceByAlias;
    }

    private function assertServiceHasMandatoryOption(array $serviceConfiguration, array $tagAttributes, string $type)
    {
        if (array_key_exists('required_options', $tagAttributes)) {
            $requiredOptions = explode(',', $tagAttributes['required_options']);
            foreach ($requiredOptions as $option) {
                if (!array_key_exists($option, $serviceConfiguration)) {
                    throw new LogicException(sprintf(
                        'The "%s" option is mandatory for %s "%s"',
                        $option,
                        $this->getName(false),
                        $type
                    ));
                }
            }
        }
    }

    private function getConstructorArguments(Definition $definition): array
    {
        $argumentsOrderByName = [];
        $class = new ReflectionClass($definition->getClass());
        $constructor = $class->getConstructor();

        if (!$constructor) {
            return [];
        }

        $parameters = $constructor->getParameters();

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

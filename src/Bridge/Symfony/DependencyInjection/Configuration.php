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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('seo_override');

        $this->addFetchersSection($rootNode);
        $this->addDomainSection($rootNode);
        $this->addEncodingSection($rootNode);

        return $treeBuilder;
    }

    private function addFetchersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('fetcher')
            ->beforeNormalization()
                ->ifTrue(function ($v) { return !isset($v['fetchers']); })
                ->then(function ($v) {
                    $v['fetchers'] = [];

                    return $v;
                })
            ->end()
            ->children()
                ->arrayNode('fetchers')
                    ->info('Fetchers should be ordered by priority. The first returning a SeoOverride for the request will be used.')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function ($v) { return ['type' => $v]; })
                        ->end()
                        ->prototype('variable')->end()
                        ->children()
                            ->scalarNode('type')
                                ->info('Type of the fetcher (see the available fetchers in the documentation)')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addDomainSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->fixXmlConfig('domain')
            ->beforeNormalization()
                ->ifTrue(function ($v) { return !isset($v['domains']); })
                ->then(function ($v) {
                    $v['domains'] = [];

                    return $v;
                })
            ->end()
            ->children()
                ->arrayNode('domains')
                    ->info('Domains should be ordered by priority. The first matching a request will be used.')
                    ->useAttributeAsKey('alias')
                    ->prototype('scalar')
                        ->info("A regexp pattern that should match against the HTTP request's host")
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addEncodingSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('encoding')
                    ->info('Encoding to use when overriding the HTML markup - see the documentation of the $encoding parameter of the htmlspecialchars function to know which encoding is supported.')
                ->end()
            ->end()
        ;
    }
}

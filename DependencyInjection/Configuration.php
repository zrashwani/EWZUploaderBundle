<?php

namespace EWZ\Bundle\UploaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ewz_uploader');

        $rootNode
            ->children()
                ->booleanNode('load_jquery')->defaultFalse()->end()
                ->arrayNode('media')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('max_size')->defaultValue('1024k')->end()
                        ->arrayNode('mime_types')
                            ->prototype('scalar')->defaultNull()->end()
                        ->end()
                        ->scalarNode('dir')->defaultValue('%kernel.root_dir%/../web')->end()
                        ->scalarNode('folder')->defaultValue('uploads')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

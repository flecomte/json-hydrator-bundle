<?php

namespace FLE\JsonHydratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('fle_json_hydrator');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('request_directory')->defaultValue('%kernel.project_dir%/sql/request')->end()
                ->scalarNode('migration_request_directory')->defaultNull()->end()
                ->scalarNode('migration_directory')->defaultValue('%kernel.project_dir%/migration')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

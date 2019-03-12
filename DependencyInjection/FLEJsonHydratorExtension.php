<?php

namespace FLE\JsonHydratorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FLEJsonHydratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('migration_directory', $config['migration_directory']);
        $container->setParameter('migration_request_directory', $config['migration_request_directory']);
        $container->setParameter('request_directory', $config['request_directory']);

        $loader        = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('connection.yaml');
        $loader->load('serializer.yaml');
        $loader->load('migration.yaml');
        $loader->load('paramconverter.yaml');
    }
}

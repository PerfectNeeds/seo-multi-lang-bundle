<?php

namespace PN\SeoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class PNSeoExtension extends Extension {

    private $alias = "pn_seo";

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->convertConfigToParameter($container, $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('parameters.yml');
    }

    private function convertConfigToParameter(ContainerBuilder $container, $config) {
        $container->setParameter($this->alias . '_class', $config['seo_class']);
        $container->setParameter($this->alias . '_translation_class', $config['seo_translation_class']);
    }

}

<?php

namespace Svyaznoy\Bundle\AuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('svyaznoy_auth');
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('site_token_endpoint')->end()
            ->scalarNode('site_token_info_endpoint')->end()
            ->scalarNode('site_admin_endpoint')->end()
            ->end();

        return $treeBuilder;
    }
}

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
            ->scalarNode('wso2is_oauth_token')->end()
            ->scalarNode('wso2is_soap_validation_endpoint')->end()
            ->scalarNode('wso2_login')->end()
            ->scalarNode('wso2_password')->end()
            ->scalarNode('wso2is_admin_endpoint')->end()
            ->end();

        return $treeBuilder;
    }
}

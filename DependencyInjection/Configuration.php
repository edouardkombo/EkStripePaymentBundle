<?php

namespace EdouardKombo\EkStripePaymentBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('ek_stripe_payment');

        $rootNode
            ->children()                 
                ->arrayNode('default')
                    ->children()
                        ->scalarNode('environment')
                            ->defaultNull()
                        ->end()                                 
                        ->scalarNode('currency')
                            ->defaultNull()
                        ->end()               
                    ->end()
                ->end()                 
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('url')
                            ->defaultNull()
                        ->end()                                 
                        ->scalarNode('checkout')
                            ->defaultNull()
                        ->end() 
                        ->scalarNode('version')
                            ->defaultNull()
                        ->end()              
                    ->end()
                ->end()                
                ->arrayNode('subUrls')
                    ->children()
                        ->scalarNode('charges')
                            ->defaultNull()
                        ->end()                                 
                        ->scalarNode('customers')
                            ->defaultNull()
                        ->end() 
                        ->scalarNode('coupons')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('plans')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('invoices')
                            ->defaultNull()
                        ->end()                
                    ->end()
                ->end()
                ->arrayNode('environments')
                    ->children()
                        ->arrayNode('test')
                            ->children()
                                ->scalarNode('secret')
                                    ->defaultNull()
                                ->end()                                 
                                ->scalarNode('publishable')
                                    ->defaultNull()
                                ->end()                
                            ->end()
                        ->end()
                        ->arrayNode('live')
                            ->children()
                                ->scalarNode('secret')
                                    ->defaultNull()
                                ->end()                                 
                                ->scalarNode('publishable')
                                    ->defaultNull()
                                ->end()                
                            ->end()
                        ->end()              
                    ->end()
                ->end()                
            ->end();

        return $treeBuilder;
    }
}

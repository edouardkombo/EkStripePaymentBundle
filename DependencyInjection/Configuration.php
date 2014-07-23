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
                ->arrayNode('test')
                    ->children()
                        ->scalarNode('secret_api')
                            ->defaultNull()
                        ->end()                                 
                        ->scalarNode('publishable_api')
                            ->defaultNull()
                        ->end()                
                    ->end()
                ->end()
                ->arrayNode('live')
                    ->children()
                        ->scalarNode('secret_api')
                            ->defaultNull()
                        ->end()                                 
                        ->scalarNode('publishable_api')
                            ->defaultNull()
                        ->end()                
                    ->end()
                ->end()
               ->scalarNode('current_environment')->end()
               ->scalarNode('api_url')->end() 
               ->scalarNode('charges_suburl')->end() 
               ->scalarNode('customers_suburl')->end()
               ->scalarNode('subscriptions_suburl')->end()
               ->scalarNode('plans_suburl')->end()  
               ->scalarNode('invoices_suburl')->end()                  
               ->scalarNode('api_checkout_url')->end()                 
               ->scalarNode('api_version')->end() 
               ->scalarNode('default_currency')->end()                 
            ->end();

        return $treeBuilder;
    }
}

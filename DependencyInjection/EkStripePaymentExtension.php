<?php

namespace EdouardKombo\EkStripePaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EkStripePaymentExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $params         = array(
            'api_url'                => $config['api_url'],
            'customers_suburl'       => $config['customers_suburl'],             
            'charges_suburl'         => $config['charges_suburl'], 
            'subscriptions_suburl'   => $config['subscriptions_suburl'],
            'plans_suburl'           => $config['plans_suburl'],
            'invoices_suburl'        => $config['invoices_suburl'],            
            'api_checkout_url'       => $config['api_checkout_url'],             
            'current_environment'    => $config['current_environment'],
            'default_currency'       => $config['default_currency'],
            'api_version'            => $config['api_version'],              
            'environments'           => array(
                'test' => $config['test'], 
                'live' => $config['live']
            )
        );
        
        $container->setParameter('ek_stripe_payment.params', $params);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}

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
        
        $params = array();
        
        //grab all configurations in camelCase
        foreach ($config as $key => $val) {
            
            foreach($val as $k => $v) {
                $keyNames = $key . ucfirst($k);
                
                if (is_array($v)) {
                    foreach ($v as $result => $r) {
                        $additionalKeyNames = $keyNames . ucfirst($result);
                        $params[$additionalKeyNames] = $r;                        
                    }
                    
                } else {
                    $params[$keyNames] = $v;                   
                }
            }
        }

        $container->setParameter('ek_stripe_payment.params', $params);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}

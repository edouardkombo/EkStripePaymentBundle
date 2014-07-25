<?php

/**
 * Main docblock
 *
 * PHP version 5
 *
 * @category  Helper
 * @package   StripePaymentBundle
 * @author    Edouard Kombo <edouard.kombo@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   GIT: 1.0.0
 * @link      http://creativcoders.wordpress.com
 * @since     0.0.0
 */
namespace EdouardKombo\EkStripePaymentBundle\Helper;

use EdouardKombo\EkStripePaymentBundle\Entity\User;

/**
 * Stripe helper methods, and base class
 *
 * @category Helper
 * @package  StripePaymentBundle
 * @author   Edouard Kombo <edouard.kombo@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://creativcoders.wordpress.com
 */
class StripeHelper
{
    /**
     *
     * @var object
     */
    public $setGetContract;
    
    /**
     *
     * @var object
     */
    public $securityContext;
    
    /**
     *
     * @var object
     */
    public $em;     
    
    /**
     *
     * @var object
     */
    public $firewall;     
    
    
    /**
     * Constructor
     * 
     * @param \EduardKombo\EkStripePaymentBundle\Contract\SetGetContract   $setGetContract  setGetContract object
     * @param array                                                        $params          Ek Stripe Payment parameters
     * @param \EduardKombo\EkStripePaymentBundle\Contract\FirewallContract $firewall        Firewall contract
     * @param SecurityContext                                              $securityContext Manage security
     * @param dobject                                                      $doctrineOrm     Doctrine Orm 
     */
    public function __construct($setGetContract, $params, $firewall, $securityContext, $doctrineOrm)
    {
        $this->firewall         = $firewall;
        $this->setGetContract   = $setGetContract;
        $this->securityContext  = $securityContext;
        $this->em               = $doctrineOrm;
        
        $this->setGetContract->cursor   = 'currentEnvironment';
        $this->setGetContract->set($params['current_environment']);
        $environment    = $this->setGetContract->currentEnvironment;
        
        $this->setGetContract->cursor   = 'apiUrl';
        $this->setGetContract->set($params['api_url']);
        
        $this->setGetContract->cursor   = 'apiVersion';
        $this->setGetContract->set($params['api_version']);
        
        $this->setGetContract->cursor   = 'apiCheckoutUrl';
        $this->setGetContract->set($params['api_checkout_url']);
        
        $this->setGetContract->cursor   = 'defaultCurrency';
        $this->setGetContract->set($params['default_currency']);
        
        $this->setGetContract->cursor   = 'secretApiKey';
        $secretApiKey   = $params['environments'][$environment]['secret_api'];
        $this->setGetContract->set($secretApiKey);
        
        $this->setGetContract->cursor   = 'publishableApiKey';
        $apiKey   = $params['environments'][$environment]['publishable_api'];
        $this->setGetContract->set($apiKey); 
        
        $this->setGetContract->cursor   = 'chargesApiUrl';
        $chargeUrl  = $params['api_url'] . $params['charges_suburl'];
        $this->setGetContract->set($chargeUrl);
        
        $this->setGetContract->cursor   = 'customersApiUrl';
        $customersUrl  = $params['api_url'] . $params['customers_suburl'];
        $this->setGetContract->set($customersUrl);
        
        $this->setGetContract->cursor   = 'plansApiUrl';
        $plansUrl  = $params['api_url'] . $params['plans_suburl'];
        $this->setGetContract->set($plansUrl);
        
        $this->setGetContract->cursor   = 'subscriptionsApiUrl';
        $subscriptionsUrl  = $params['api_url'] . $params['plans_suburl'];
        $this->setGetContract->set($subscriptionsUrl);
        
        $this->setGetContract->cursor   = 'invoicesApiUrl';
        $invoicesUrl  = $params['api_url'] . $params['invoices_suburl'];
        $this->setGetContract->set($invoicesUrl);         
        
        $user_agent = [
            'bindings_version' => '1.0.0',
            'lang'             => 'php',
            'lang_version'     => PHP_VERSION,
            'publisher'        => 'scribe',
            'uname'            => php_uname(),
        ];
        $headers = [
            'X-Stripe-Client-User-Agent: '    . json_encode($user_agent),
            'User-Agent: Stripe/v1 ScribeStripeBundle/' . '1.0.0',
            'Authorization: Bearer '          . $secretApiKey,
            'Stripe-Version: '                . $this->setGetContract->apiVersion
        ];        
        $this->setGetContract->cursor   = 'headers';
        $this->setGetContract->set($headers);                  
    }
    
    
    /**
     * Return stripe user id in local database
     * 
     * @return mixed
     */
    public function getStripeUserId()
    {
        $user       = $this->securityContext->getToken()->getUser();        
        $userId     = $user->getId();
        
        $stripe     = $this->em->getRepository('EkStripePaymentBundle:User')
                ->findOneByUser($userId);
        
        if (!$stripe) {
            $result = false;
        } else {
            $result = $stripe->getStripeUserId();
        }
        
        return $result;
    }
    
    
    /**
     * Receive Stripe UserId from Stripe Api
     * 
     * @param array  $request        Requeest received from Stripe Api
     * @param string $customerId     Current customerId
     * @param string $stripeProperty Stripe property to call
     * 
     * @return mixed
     */
    public function receiveStripeUserId($request, $customerId, $stripeProperty)
    {
        if (($stripeProperty === 'customersApiUrl')) {
            $result = (isset($request['id'])) ? $request['id'] : $customerId;
        } else {
            $result = false;
        }
        
        return $result;
    }    
    
    
    /**
     * Set Stripe user id linked to current user
     * 
     * @return mixed
     */
    public function setStripeUserId($userId)
    {
        $user       = $this->securityContext->getToken()->getUser();  
        $entity     = new User();
        
        $entity->setStripeUserId($userId);
        $entity->setUser($user);
        $this->em->persist($entity);
        $this->flush(); 
        
        return true;
    }    
    
    
    /**
     * Return correct url without params to dispatch to cUrl
     * 
     * @param string $property SetGetContract property
     * 
     * @return string
     */
    public function getUrlWithoutParams($property)
    {
        $customersApiUrl    = $this->setGetContract->customersApiUrl;
        $stripeUserId       = $this->getStripeUserId();
        
        switch($property) {
            case 'subscriptionsApiUrl':
                $this->firewall->isStripeUserValid($stripeUserId);
                $result = $customersApiUrl."/$stripeUserId/subscriptions";
                break;
            default:
                $result = $this->setGetContract->{$property};
                break;
        }
        
        return $result;        
    }
}

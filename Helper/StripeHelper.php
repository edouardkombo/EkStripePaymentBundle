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
     * @param \EdouardKombo\EkStripePaymentBundle\Contract\SetGetContract   $setGetContract  setGetContract object
     * @param array                                                         $params          Ek Stripe Payment parameters
     * @param \EdouardKombo\EkStripePaymentBundle\Contract\FirewallContract $firewall        Firewall contract
     * @param SecurityContext                                               $securityContext Manage security
     * @param dobject                                                       $doctrineOrm     Doctrine Orm 
     */
    public function __construct($setGetContract, $params, $firewall, 
            $securityContext, $doctrineOrm)
    {
        $this->firewall         = $firewall;
        $this->setGetContract   = $setGetContract;
        $this->securityContext  = $securityContext;
        $this->em               = $doctrineOrm;
        
        foreach ($params as $key => $val) {
            $this->setGetContract->setParameter($key, $val);
        }

        $this->setGetContract->setParameter('subUrlsSubscriptions', 
                $this->setGetContract->subUrlsCustomers);         
        
        if ($this->setGetContract->defaultEnvironment === 'test') {
            $secretKey      = $this->setGetContract->environmentsTestSecret;
            $publishableKey = $this->setGetContract->environmentsTestPublishable;
        } else {
            $secretKey      = $this->setGetContract->environmentsLiveSecret;
            $publishableKey = $this->setGetContract->environmentsLivePublishable;
        }
        
        $this->setGetContract->setParameter('secretApiKey', $secretKey);
        $this->setGetContract->setParameter('publishableApiKey', $publishableKey);
        $this->setHeaders($secretKey);               
    }
    
    /**
     * Set http headers to send to cUrl
     */
    private function setHeaders()
    {
        $secretApiKey   = $this->setGetContract->secretApiKey;
        $apiVersion     = $this->setGetContract->apiVersion;   
            
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
            'Stripe-Version: '                . $apiVersion
        ];
        
        $this->setGetContract->setParameter('headers', $headers);       
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
     * @param string $subUrl         Current subUrl
     * 
     * @return mixed
     */
    public function receiveStripeUserId($request, $customerId, $subUrl)
    {
        if (($subUrl === 'subUrlsCustomers')) {
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
}

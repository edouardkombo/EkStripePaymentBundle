<?php

/**
 * Main docblock
 *
 * PHP version 5
 *
 * @category  Contract
 * @package   EkStripePaymentBundle
 * @author    Edouard Kombo <edouard.kombo@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   GIT: 1.0.0
 * @link      http://creativcoders.wordpress.com
 * @since     0.0.0
 */
namespace EdouardKombo\EkStripePaymentBundle\Contract;

use EdouardKombo\PhpObjectsContractBundle\Contract\Elements\Abstractions\CommunicationAbstractions;

/**
 * StripePayment Communication Contract
 *
 * @category Contract
 * @package  EkStripePaymentBundle
 * @author   Edouard Kombo <edouard.kombo@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://creativcoders.wordpress.com
 */
class CommunicationContract extends CommunicationAbstractions
{
         
    /**
     *
     * @var mixed
     */
    public $httpResponse;
    
    /**
     *
     * @var \EdouardKombo\EkStripePaymentBundle\Helper\StripeHelper
     */
    public $helper;
    
    /**
     *
     * @var object Curl service
     */
    public $curl;
    
    /**
     *
     * @var array Curl datas
     */
    public $curlDatas;
    
    /**
     *
     * @var string
     */
    public $customerId;    
    
    /**
     *
     * @var string Stripe property to target
     */
    public $stripeProperty;     
    
    /**
     * Constructor
     * 
     * @param \EdouardKombo\EkStripePaymentBundle\Helper\StripeHelper $helper Helper class
     */
    public function __construct($helper)
    {
        $this->helper = $helper;
    }
    
    
    /**
     * Check if property exists and set the value to the property
     * 
     * @param string $property Property we want to reach
     * @param mixed  $value    Value to assign to the property
     * 
     * @return \EdouardKombo\EkStripePaymentBundle\Helper\StripeHelper
     */
    public function setParameter($property, $value)
    {
        $setGetContract = $this->helper->setGetContract;      
        
        $setGetContract->cursor = $property;
        $setGetContract->set($value);
        
        return $this;
    } 
    
    /**
     * Send something
     * 
     * @return mixed
     */
    public function send()
    {
        $setGetContract = $this->helper->setGetContract;
        $firewall       = $this->helper->firewall;
        
        $url    = $this->helper->getUrlWithoutParams($this->stripeProperty);
        
        $this->curl->setParameter('url',     $url); 
        $this->curl->setParameter('headers', $setGetContract->headers);       
        $this->curl->setParameter('datas',   $this->curlDatas);            

        $request        = $this->curl->post();
        
        $firewall->handleStripeError($request[0], $request[1]);
        
        return $this->helper->receiveStripeUserId($request[0], $this->customerId, 
                $this->stripeProperty);        
    }
    
    /**
     * Receive http status code from cUrl
     * 
     * @return mixed
     */
    public function receive()
    {
        $firewall       = $this->helper->firewall;
        
        $httpResponse   = $this->httpResponse[0];
        $httpCode       = $this->httpResponse[1];
        
        return $firewall->handleStripeError($httpResponse, $httpCode);
    }   
}
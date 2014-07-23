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
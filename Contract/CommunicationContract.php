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
     * @var \EdouardKombo\EkStripePaymentBundle\Contract\SetGet
     */
    public $setGetContract;    
    
    /**
     *
     * @var \EdouardKombo\EkStripePaymentBundle\Contract\FirewallContract
     */
    public $firewall;    
    
    /**
     *
     * @var object Curl service
     */
    public $curl;
   
    
    /**
     * Constructor
     * 
     * @param \EdouardKommbo\EkApiCallerBundle\Contract\HttpContract  $curl   Curl object
     * @param \EdouardKombo\EkStripePaymentBundle\Helper\StripeHelper $helper Helper class
     * @param \EdouardKommbo\EkStripePaymentBundle\Contract\FirewallContract      $firewall  Security
     */
    public function __construct($curl, $helper, $firewall)
    {
        $this->curl             = $curl;
        $this->helper           = $helper;
        $this->setGetContract   = $this->helper->setGetContract;
        $this->firewall         = $firewall;
    } 
    
    /**
     * Send something
     * 
     * @return mixed
     */
    public function send()
    {
        $this->curl->setParameter('url',     $this->setGetContract->urlToRequest); 
        $this->curl->setParameter('headers', $this->setGetContract->headers);       
        $this->curl->setParameter('datas',   $this->setGetContract->datasToRequest);            
        
        $customerId         = $this->setGetContract->customerId;        
        $methodToRequest    = $this->setGetContract->methodToRequest;
        $currentSubUrl      = $this->setGetContract->currentSubUrl;
        
        $this->httpResponse = $this->curl->{$methodToRequest}();
        
        $this->receive();
        
        return $this->helper->receiveStripeUserId($this->httpResponse[0], 
            $customerId, 
            $currentSubUrl);        
    }
    
    /**
     * Receive http status code from cUrl
     * 
     * @return mixed
     */
    public function receive()
    {
        $httpResponse   = $this->httpResponse[0];
        $httpCode       = $this->httpResponse[1];
        
        return $this->firewall->handleStripeError($httpResponse, $httpCode);
    }   
}
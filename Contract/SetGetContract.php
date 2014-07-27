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

use EdouardKombo\PhpObjectsContractBundle\Contract\Elements\Abstractions\SetGetAbstractions;

/**
 * StripePayment SetGet Contract
 *
 * @category Contract
 * @package  EkStripePaymentBundle
 * @author   Edouard Kombo <edouard.kombo@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://creativcoders.wordpress.com
 */
class SetGetContract extends SetGetAbstractions
{
    
    /**
     *
     * @var string
     */
    public $secretApiKey = '';
    
    /**
     *
     * @var string
     */
    public $publishableApiKey = '';
    
    /**
     *
     * @var array
     */
    public $headers = array();     
    
    /**
     *
     * @var string
     */
    public $defaultEnvironment = '';
    
    /**
     *
     * @var string
     */
    public $defaultCurrency = '';  

    /**
     *
     * @var string
     */
    public $apiUrl = '';
    
    /**
     *
     * @var string
     */
    public $subUrlsCharges = ''; 
    
    /**
     *
     * @var string
     */
    public $subUrlsInvoices = '';
    
    /**
     *
     * @var string
     */
    public $subUrlsCoupons = '';    
    
    /**
     *
     * @var string
     */
    public $subUrlsPlans = '';
    
    /**
     *
     * @var string
     */
    public $subUrlsSubscriptions = '';
    
    /**
     *
     * @var string
     */
    public $subUrlsCustomers = '';    
    
    /**
     *
     * @var string
     */
    public $apiCheckout = '';
    
    /**
     *
     * @var string
     */
    public $apiVersion = '';
    
    /**
     *
     * @var string
     */
    public $environmentsTest = ''; 
    
    /**
     *
     * @var string
     */
    public $environmentsLive = ''; 
    
     /**
     *
     * @var string
     */
    public $environmentsTestSecret = ''; 
    
    /**
     *
     * @var string
     */
    public $environmentsTestPublishable = ''; 
    
     /**
     *
     * @var string
     */
    public $environmentsLiveSecret = ''; 
    
    /**
     *
     * @var string
     */
    public $environmentsLivePublishable = '';
    
    /**
     *
     * @var string
     */
    public $urlToRequest = '';  
    
    /**
     *
     * @var string
     */
    public $datasToRequest = ''; 
    
    /**
     *
     * @var string
     */
    public $methodToRequest = ''; 
    
    /**
     *
     * @var string
     */
    public $customerId = '';
    
    /**
     *
     * @var string
     */
    public $currentSubUrl = '';     

    /**
     *
     * @var string 
     */
    public $cursor = ''; 
    
    /**
     *
     * @var object 
     */
    private $firewall = '';     
    
    
    /**
     * Constructor
     * 
     * @param \EdouardKombo\EkStripePaymentBundle\Contract\FirewallContract Firewall class
     */
    public function __construct($firewall)
    {
        $this->firewall = $firewall;
    }
    
    /**
     * Check if property exists and set the value to the property
     * 
     * @param string $property Property we want to reach
     * @param mixed  $value    Value to assign to the property
     * 
     * @return \EdouardKombo\EkStripePaymentBundle\Contract\SetGetContract
     */
    public function setParameter($property, $value)
    {
        $this->cursor = $property;
        $this->set($value);
        
        return $this;
    }    
    
    /**
     * Set a value
     * 
     * @param mixed $value Value to be setted
     * 
     * @return \EdouardKombo\EkStripePaymentBundle\Contract\SetGetContract
     */
    public function set($value)
    {
        $this->secure();
        
        $this->{$this->cursor} = $value;  
        
        return $this;
    }
    
    /**
     * Get a value
     * 
     * @return mixed
     */
    public function get()
    {
        $this->secure();
        
        return $this->{$this->cursor};        
    }
    
    /**
     * Secure by checking properties existence
     * 
     * @return mixed
     */
    public function secure()
    {
        return $this->firewall->checkIfPropertyExists($this->cursor, $this);
    }
    
}
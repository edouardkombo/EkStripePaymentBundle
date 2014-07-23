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
    public $currentEnvironment = '';
    
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
    public $chargesApiUrl = ''; 
    
    /**
     *
     * @var string
     */
    public $invoicesApiUrl = '';
    
    /**
     *
     * @var string
     */
    public $plansApiUrl = '';
    
    /**
     *
     * @var string
     */
    public $subscriptionsApiUrl = '';
    
    /**
     *
     * @var string
     */
    public $customersApiUrl = '';    
    
    /**
     *
     * @var string
     */
    public $apiCheckoutUrl = '';
    
    /**
     *
     * @var string
     */
    public $apiVersion = '';

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
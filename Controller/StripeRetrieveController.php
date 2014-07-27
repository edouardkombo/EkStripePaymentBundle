<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Stripe retrieve methods controller.
 *
 */
class StripeRetrieveController extends Controller
{
 
    /**
     *
     * @var object
     */
    public $container;   
    
    /**
     *
     * @var \EdouardKommbo\EkStripePaymentBundle\Contract\SetGetContract
     */
    public $setGetContract; 
    
    /**
     *
     * @var string
     */
    public $customerId;
    
    /**
     *
     * @var string
     */
    public $url;
    
    /**
     *
     * @var string
     */
    public $datas;
    
    /**
     *
     * @var string
     */
    public $method = 'post';
    
    /**
     *
     * @var string
     */
    public $currentSubUrl;    
    
    
    /**
     *
     * @var \EdouardKommbo\EkStripePaymentBundle\Contract\CommunicationContract
     */
    public $communicationContract;   
    

    /**
     * Constructor
     * 
     * @param object                                                              $container Service container
     * @param \EdouardKommbo\EkStripePaymentBundle\Helper\StripeHelper            $helper    helper methods
     * @param \EdouardKommbo\EkStripePaymentBundle\Contract\CommunicationContract $comm      Send and receive gateway
     * 
     */
    public function __construct($container, $helper, $comm)
    {
        $this->container                = $container;
        $this->setGetContract           = $helper->setGetContract;
        $this->communicationContract    = $comm;
    }
    
    /**
     * Retrieve a specific plan or the list of all plans
     * 
     * @return array
     */
    public function retrievePlansAction()
    {       
        $id         = $this->container->get('request')->get('id');
        $baseUrl    = $this->setGetContract->subUrlsPlans;
        $this->url              = (isset($id)) ? $baseUrl."/$id" : $baseUrl;
        $this->currentSubUrl    = 'subUrlsPlans';
        $this->datas            = array();
        
        return $this->prepareRequest();
    }
    
    /**
     * Retrieve a specific customer or complete list of customer
     * 
     * @return array
     */
    public function retrieveCustomersAction()
    {
        $id         = $this->container->get('request')->get('id');
        $baseUrl    = $this->setGetContract->subUrlsCustomers;
        $this->url              = (isset($id)) ? $baseUrl."/$id" : $baseUrl;
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = array();
        
        return $this->prepareRequest();                        
    }
    
    /**
     * Retrieve a specific charge or complete list of charges
     * 
     * @return array
     */
    public function retrieveChargesAction()
    {
        $id         = $this->container->get('request')->get('id');
        $baseUrl    = $this->setGetContract->subUrlsCharges;
        $this->url              = (isset($id)) ? $baseUrl."/$id" : $baseUrl;
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas            = array();
        
        return $this->prepareRequest();                        
    }
    
    /**
     * Retrieve a specific refund or complete list of refunds
     * GET method is only allowed
     * 
     * @return array
     */
    public function retrieveRefundsAction()
    {
        $chargesId  = $this->container->get('request')->get('chargesid');
        $refundsId  = $this->container->get('request')->get('refundsId');        
        $baseUrl    = $this->setGetContract->subUrlsCharges."/$chargesId";
        $this->url      = (isset($refundsId)) ? $baseUrl."/$refundsId" : $baseUrl;
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas            = array();
        $this->method           = 'get';       
        
        return $this->prepareRequest();                        
    }    
    
    /**
     * Retrieve a specific subscription or complete list
     * 
     * @return array
     */
    public function retrieveSubscriptionsAction()
    {
        $subId      = $this->container->get('request')->get('subId');        
        $baseUrl    = $this->setGetContract->subUrlsCustomers."/$this->customerId";
        $this->url              = $baseUrl."/subscriptions/$subId";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = array();
        
        return $this->prepareRequest();                          
    } 
    
    /**
     * Retrieve an invoice or all invoices
     * 
     * @return array
     */
    public function retrieveInvoicesAction()
    {
        $invoiceId  = $this->container->get('request')->get('id');        
        $baseUrl    = $this->setGetContract->subUrlsInvoices."/$invoiceId";
        $customerUrl= $baseUrl."?customer=$this->customerId";
        $this->url        = (isset($this->customerId)) ? $baseUrl : $customerUrl;
        $this->currentSubUrl    = 'subUrlsInvoices';
        $this->datas            = array();
        
        return $this->prepareRequest();                          
    }
    
    /**
     * Retrieve a coupon or list of coupons
     * 
     * @return array
     */
    public function retrieveCouponsAction()
    {
        $couponId   = $this->container->get('request')->get('id');        
        $baseUrl    = $this->setGetContract->subUrlsCoupons;
        $this->url        = (isset($couponId)) ? $baseUrl : $baseUrl."/$couponId";
        $this->currentSubUrl    = 'subUrlsCoupons';
        $this->datas            = array();
        
        return $this->prepareRequest();                          
    }
    
    /**
     * Call communication contract to send the request
     * 
     * @return boolean
     */
    private function prepareRequest()
    {
        $this->setGetContract->setparameter('urlToRequest', $this->url);
        $this->setGetContract->setparameter('datasToRequest', $this->datas);
        $this->setGetContract->setparameter('methodToRequest', $this->method);
        $this->setGetContract->setparameter('customerId', $this->customerId);
        $this->setGetContract->setParameter('currentSubUrl', $this->currentSubUrl);
        
        $id = $this->communicationContract->send();
        
        $this->customerId = (false === $id) ?  $this->customerId : $id;
        
        return true;
    }    
}

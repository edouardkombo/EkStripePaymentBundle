<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Stripe delete methods controller.
 *
 */
class StripeDeleteController extends Controller
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
    public $method = 'delete';
    
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
     * Delete stripe plan
     * DELETE method only
     * 
     * @return array
     */
    public function deletePlansAction()
    {            
        $id     = $this->container->get('request')->get('id');
        
        $this->url    = (string) $this->setGetContract->subUrlsPlans . "/$id";
        $this->currentSubUrl    = 'subUrlsPlans';
        $this->datas            = array();
        
        return $this->prepareRequest();
    }
    
    /**
     * Delete a customer
     * DELETE method only
     * 
     * @return array
     */
    public function deleteCustomersAction()
    {
        $id     = $this->container->get('request')->get('id');
        
        $this->url    = (string) $this->setGetContract->subUrlsCustomers . "/$id";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = array();
        
        return $this->prepareRequest();                         
    }    
    
    /**
     * Cancel a subscription
     * DELETE method only
     * 
     * @return array
     */
    public function deleteSubscriptionsAction()
    {
        $subId      = $this->container->get('request')->get('subId'); 
        $baseUrl    = $this->setGetContract->subUrlsCustomers."/$this->customerId";
        $this->url        = $baseUrl."/subscriptions/$subId";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = array();
        
        return $this->prepareRequest();                            
    }
    
    /**
     * Delete a coupon
     * DELETE method only
     * 
     * @return array
     */
    public function deleteCouponsAction()
    {
        $couponId   = $this->container->get('request')->get('id'); 
        $baseUrl    = $this->setGetContract->subUrlsCoupons;
        $this->url        = $baseUrl."/$couponId";
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

<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Stripe add methods controller.
 *
 */
class StripeAddController extends Controller
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
     * Create stripe plan
     * 
     * @return mixed
     */
    public function addPlansAction()
    {
        $amount         = $this->container->get('request')->get('amount');
        $interval       = $this->container->get('request')->get('interval');        
        $name           = $this->container->get('request')->get('name');
        $currency       = $this->container->get('request')->get('currency');        
        $id             = $this->container->get('request')->get('id');
        
        $this->url              = $this->setGetContract->subUrlsPlans;
        $this->currentSubUrl    = 'subUrlsPlans';
        $this->datas            = [
            "amount"    => $amount,
            "interval"  => $interval,
            "name"      => $name,
            "currency"  => $currency,
            "id"        => $id            
        ];
        
        return $this->prepareRequest();
        
    }
    
    /**
     * Create a stripe customer
     * 
     * @return array
     */
    public function addCustomersAction()
    {
        $token        = $this->container->get('request')->get('stripeToken');
        $userEmail    = $this->container->get('request')->get('stripeEmail');
        $description  = $this->container->get('request')->get('description');
        
        $this->url          = (string) $this->setGetContract->subUrlsCustomers;
        $this->currentSubUrl= 'subUrlsCustomers';
        $this->datas        = (array) [
            "card"          => $token,
            "email"         => $userEmail,
            "description"   => $description            
        ];
        
        return $this->prepareRequest();
    }
    
    /**
     * Add a charge
     * 
     * @return array
     */
    public function addChargesAction()
    {
        $amount         = $this->container->get('request')->get('amount');
        $this->url              = $this->setGetContract->subUrlsCharges;
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas            = (array) array(
            'amount'        => $amount,
            'currency'      => $this->setGetContract->defaultCurrency,
            'customer'      => $this->customerId,        
        );
        
        return $this->prepareRequest();
    } 
    
    /**
     * Add a refund corresponding to a charge.
     * POST method is mandatory
     * 
     * @return array
     */
    public function addRefundsAction()
    {
        $id     = $this->container->get('request')->get('chargesId');
        $this->url    = $this->setGetContract->subUrlsCharges."/$id/refunds";
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas  = (array) array();
        
        return $this->prepareRequest();
    }     
    
    /**
     * Add a subscription to a user
     * 
     * @return array
     */
    public function addSubscriptionsAction()
    {
        $plan       = $this->container->get('request')->get('plan');
        $baseUrl    = $this->setGetContract->subUrlsCustomers;
        $this->url              = $baseUrl."/$this->customerId";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = (array) array('plan'  => $plan);
        
        return $this->prepareRequest();                         
    }
    
    /**
     * Add an invoice
     * 
     * @return array
     */
    public function addInvoicesAction()
    {
        $baseUrl                = $this->setGetContract->subUrlsInvoices;
        $this->url              = $baseUrl;
        $this->currentSubUrl    = 'subUrlsInvoices';
        $this->datas    = (array) array('customer'  => $this->customerId);
        
        return $this->prepareRequest();                         
    }
    
    /**
     * Add a coupon
     * 
     * @return array
     */
    public function addCouponsAction()
    {
        $percent    = $this->container->get('request')->get('percent');
        $duration   = $this->container->get('request')->get('duration');
        $dIM        = $this->container->get('request')->get('durationIM'); 
        $id         = $this->container->get('request')->get('id');         
        $baseUrl    = $this->setGetContract->subUrlsCoupons;
        $this->url              = $baseUrl;
        $this->currentSubUrl    = 'subUrlsCoupons';
        $this->datas            = (array) array(
            'percent_off'       => $percent,
            'duration'          => $duration,
            'duration_in_months'=> $dIM,
            'id'                => $id
        );
        
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

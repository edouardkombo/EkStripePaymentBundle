<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Stripe update methods controller.
 *
 */
class StripeUpdateController extends Controller
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
     * Update stripe plan
     * 
     * @return array
     */
    public function updatePlansAction()
    {       
        $name   = $this->container->get('request')->get('name');       
        $id     = $this->container->get('request')->get('id');
        
        $this->url    = (string) $this->setGetContract->subUrlsPlans . "/$id";
        $this->currentSubUrl    = 'subUrlsPlans';
        $this->datas            = (array) array("name" => $name);
        
        return $this->prepareRequest();
    }
    
    /**
     * Update a customer
     * 
     * @return array
     */
    public function updateCustomersAction()
    {
        $description   = $this->container->get('request')->get('description');       
        $id     = $this->container->get('request')->get('id');
        
        $this->url    = (string) $this->setGetContract->subUrlsCustomers . "/$id";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->datas            = (array) array("description" => $description);
        
        return $this->prepareRequest();                        
    }
    
    /**
     * Update a charge
     * 
     * @return array
     */
    public function updateChargesAction()
    {
        $description   = $this->container->get('request')->get('description');       
        $id     = $this->container->get('request')->get('id');
        
        $this->url    = (string) $this->setGetContract->subUrlsCharges . "/$id";
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas            = (array) array("description" => $description);
        
        return $this->prepareRequest();                        
    }
    
    /**
     * Update a refund by adding additional informations
     * 
     * @return array
     */
    public function updateRefundsAction()
    {
        $chargesId      = $this->container->get('request')->get('chargesid');
        $refundsId      = $this->container->get('request')->get('refundsId');
        $informations   = $this->container->get('request')->get('infos');
        $baseUrl        = $this->setGetContract->subUrlsCharges."/$chargesId";
        $this->url              = $baseUrl."/$refundsId";
        $this->currentSubUrl    = 'subUrlsCharges';
        $this->datas            = array("meta['infos']" => "$informations");
        
        return $this->prepareRequest();                        
    }    
    
    /**
     * Add subscriptions method, to subscribe a user to a plan
     * 
     * @return array
     */
    public function updateSubscriptionsAction()
    {
        $plan       = $this->container->get('request')->get('plan');
        $subId      = $this->container->get('request')->get('subId'); 
        $baseUrl    = $this->setGetContract->subUrlsCustomers."/$this->customerId";
        $this->currentSubUrl    = 'subUrlsCustomers';
        $this->url              = $baseUrl."/subscriptions/$subId";
        $this->datas            = array('plan' => $plan);
        
        return $this->prepareRequest();                           
    }
    
    /**
     * Close an invoice
     * 
     * @return array
     */
    public function updateInvoicesAction()
    {
        $invoiceId  = $this->container->get('request')->get('id');        
        $baseUrl    = $this->setGetContract->subUrlsInvoices."/$invoiceId";
        $this->url        = $baseUrl;
        $this->currentSubUrl    = 'subUrlsInvoices';
        $this->datas            = array('closed' => true);
        
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

<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Stripe controller.
 *
 */
class StripeController extends Controller
{
    
    /**
     *
     * @var string
     */
    protected $customerId;
    
    /**
     *
     * @var string
     */
    protected $currency;
    
    /**
     *
     * @var array
     */
    protected $datas;
    
    /**
     *
     * @var object
     */
    protected $helper;
    
    /**
     *
     * @var string
     */
    protected $stripeProperty;
    
    /**
     *
     * @var object
     */
    protected $container;     
    

    /**
     * 
     * @param object $container Service container
     */
    public function __construct($container)
    {
        $this->container = $container;        
    }
    
    /**
     * Lists all Card entities.
     *
     */
    public function indexAction()
    {       
        $em     = $this->getDoctrine()->getManager();

        $helper = $this->container->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        
        $entities = $em->getRepository('EkStripePaymentBundle:User')->findAll();

        return $this->render('EkStripePaymentBundle:User:index.html.twig', array(
            'entities'      => $entities,
            'checkout_url'  => $setGetContract->apiCheckoutUrl,
            'user_email'    => $this->container->getUser()->getEmail(),
            'currency'      => $setGetContract->defaultCurrency,
            'api_key'       => $setGetContract->publishableApiKey
        ));
    }
    
    /**
     * Creates a new Card entity.
     *
     */
    public function createAction()
    {
        $this->helper           = $this->container->get('ek_stripe_payment.helper.stripe');        
        $this->customerId       = $this->helper->getStripeUserId();
        $this->stripeProperty   = $this->container->get('request')->get('type');
        
        $this->dispatchSubActions();            

        return $this->render('EkStripePaymentBundle:User:new.html.twig');
    }
    
    /**
     * Dispatch corresponding actions
     * 
     */
    private function dispatchSubActions()
    {   
        switch($this->stripeProperty) {
            case 'subscriptionsApiUrl':
                $subscription   = $this->container->get('request')->get($this->stripeProperty);
                if (!empty($subscription)) {
                    $this->stripeSubscriptionsAction();
                }
                break;
                
            case 'customersApiUrl':
                if (!$this->customerId) {
                    $this->stripeCustomersAction();
                    $this->helper->setStripeUserId($this->customerId);               
                }                
                break;
            case 'chargesApiUrl':
                if (!$this->customerId) {
                    $this->stripeCustomersAction();
                    $this->helper->setStripeUserId($this->customerId);               
                }
                $this->stripeChargesAction();
                break;
        }
    }
    
    /**
     * Create stripe plan
     * 
     * @return mixed
     */
    public function stripePlansAction()
    {
        $amount         = $this->container->get('request')->get('amount');
        $interval       = $this->container->get('request')->get('interval');        
        $name           = $this->container->get('request')->get('name');
        $currency       = $this->container->get('request')->get('currency');        
        $id             = $this->container->get('request')->get('id');
        
        $this->datas = [
            "amount"    => $amount,
            "interval"  => $interval,
            "name"      => $name,
            "currency"  => $currency,
            "id"        => $id            
        ];
        
        return $this->sendStripeRequest();          
    }
    
    /**
     * Create a stripe customer and/or subscribes him to a plan
     * 
     * @return mixed
     */
    public function stripeCustomersAction()
    {
        $stripeToken        = $this->container->get('request')->get('stripeToken');
        $stripeUserEmail    = $this->container->get('request')->get('stripeEmail');       
        
        $this->datas = [
            "card"          => $stripeToken,
            "email"         => $stripeUserEmail,
            "description"   => 'User wrapper'            
        ];                

        return $this->sendStripeRequest();          
    }
    
    /**
     * Create a stripe customer and/or subscribes him to a plan
     * 
     * @return mixed
     */
    public function stripeSubscriptionsAction()
    {
        $this->datas = [
            "plan"          => $this->container->get('request')->get('plan')
        ];                

        return $this->sendStripeRequest();          
    }    
    
    /**
     * Create a charge
     * 
     * @return mixed
     */
    private function stripeChargesAction()
    {
        $helper         = $this->container->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        $amount         = $this->container->get('request')->get('amount');
        
        if (empty($amount)) {
            return false;
        }
        
        $this->datas = [
            'amount'        => $amount,
            'currency'      => $setGetContract->defaultCurrency,
            'customer'      => $this->customerId,        
        ];        

        $this->sendStripeRequest();
    }    
    
    /**
     * Call communication contract to send the request
     * 
     * @return boolean
     */
    private function sendStripeRequest()
    {
        $communication = $this->container->get('ek_stripe_payment.contract.communication');
        $communication->curl = $this->container->get('ek_api_caller.contract.http');
        $communication->stripeProperty = $this->stripeProperty;
        $communication->curlDatas      = $this->datas;
        $communication->customerId     = $this->customerId;
        $id = $communication->send();
        
        $this->customerId = (false === $id) ?  $this->customerId : $id;
        
        return true;
    }    
}

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
     * Examples of different kinds of stripe forms
     *
     */
    public function indexAction()
    {       
        $helper         = $this->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        
        $amount         = 25;
        $stripeAmount   = $amount * 100;
        
        return $this->render('EkStripePaymentBundle::examples.html.twig', array(
            'stripe-checkout-url'      => $setGetContract->apiCheckoutUrl,
            'stripe-user-email'        => $this->getUser()->getEmail(),
            'stripe-form-action-url'   => 'ek_stripe_payment_card_create',
            'stripe-data-name'         => 'My company',
            'stripe-data-description'  => 'Payment test ',
            'stripe-data-panel-label'  => 'Instant payment',
            'stripe-data-label'        => 'Instant payment',
            'stripe-plan-interval'     => 'month',
            'stripe-plan-name'         => 'Test plan description',
            'stripe-plan-id'           => 'Silver',
            'stripe-coupons-id'        => '20percent', 
            'stripe-coupons-percent'   => '25',
            'stripe-coupons-duration'  => 'repeated',
            'stripe-coupons-durationIM'=> 2,            
            'stripe-data-amount'       => $stripeAmount,
            'stripe-normal-data-amount'=> $amount,            
            'stripe-currency'          => $setGetContract->defaultCurrency,
            'stripe-api-key'           => $setGetContract->publishableApiKey
        ));
    }
    
    /**
     * Create a new call to stripe entity
     *
     */
    public function createAction()
    {
        $method         = $this->get('request')->get('method');
        $action         = $this->get('request')->get('action');
        $serviceName    = "ek_stripe_payment.controller.stripe.$method";
        $targetedMethod = $method.$action."Action";

        $this->helper           = $this->get('ek_stripe_payment.helper.stripe');        
        $this->customerId       = $this->helper->getStripeUserId();        
        
        $service                = $this->get($serviceName);
        $service->customerId    = $this->customerId;
        
        $this->dispatchSubActions($service, $targetedMethod);            

        return $this->render('EkStripePaymentBundle::payment_granted.html.twig');
    }
    
    /**
     * Dispatch corresponding actions
     * 
     */
    private function dispatchSubActions($service, $targetedMethod)
    {   
        switch($targetedMethod) {
            case 'addChargesAction':
                if (!$this->customerId) {
                    $service->addCustomersAction();              
                }
                $service->{$targetedMethod}();
                break;            
            case 'addSubscriptionsAction':
                $plan   = $this->get('request')->get('plan');
                if (!empty($plan)) {
                    $service->{$targetedMethod}();
                }
                break; 
            case 'addCustomersAction':
                if (!$this->customerId) {
                    $service->{$targetedMethod}();             
                }                
                break;
            default:
                $service->{$targetedMethod}();
                break;
        }
    }      
}

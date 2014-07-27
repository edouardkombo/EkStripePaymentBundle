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
        
        $user           = $this->get('security.context')->getToken()->getUser();
        $userMail       = $user->getEmail();
        
        $amount         = 25;
        $stripeAmount   = $amount * 100;
        
        return $this->render('EkStripePaymentBundle::examples.html.twig', array(
            'stripe_checkout_url'      => $setGetContract->apiCheckout,
            'stripe_user_email'        => $userMail,
            'stripe_form_action_url'   => 'ek_stripe_payment_card_create',
            'stripe_data_name'         => 'My company',
            'stripe_data_description'  => 'Payment test ',
            'stripe_data_panel_label'  => 'Instant payment',
            'stripe_data_label'        => 'Instant payment',
            'stripe_plan_interval'     => 'month',
            'stripe_plan_name'         => 'Test plan description',
            'stripe_plan_id'           => 'Silver',
            'stripe_coupons_id'        => '20percent', 
            'stripe_coupons_percent'   => '25',
            'stripe_coupons_duration'  => 'repeated',
            'stripe_coupons_durationIM'=> 2,            
            'stripe_data_amount'       => $stripeAmount,
            'stripe_normal_data_amount'=> $amount,            
            'stripe_currency'          => $setGetContract->defaultCurrency,
            'stripe_currency_letter'   => '€',
            'stripe_api_key'           => $setGetContract->publishableApiKey
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

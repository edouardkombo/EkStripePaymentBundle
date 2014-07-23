<?php

namespace EdouardKombo\EkStripePaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use EdouardKombo\EkStripePaymentBundle\Entity\User;

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
     * Lists all Card entities.
     *
     */
    public function indexAction()
    {       
        $em = $this->getDoctrine()->getManager();

        $helper = $this->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        
        $entities = $em->getRepository('EkStripePaymentBundle:User')->findAll();

        return $this->render('EkStripePaymentBundle:User:index.html.twig', array(
            'entities'      => $entities,
            'checkout_url'  => $setGetContract->apiCheckoutUrl,
            'user_email'    => $this->getUser()->getEmail(),
            'currency'      => $setGetContract->defaultCurrency,
            'api_key'       => $setGetContract->publishableApiKey
        ));
    }
    
    
    /**
     * Create stripe plan
     * 
     * @return mixed
     */
    public function createPlanAction()
    {
        $request        = $this->getRequest();
        
        $amount         = $request->get('amount');
        $interval       = $request->get('interval');        
        $name           = $request->get('name');
        $currency       = $request->get('currency');        
        $id             = $request->get('id');
        
        $datas = [
            "amount"    => $amount,
            "interval"  => $interval,
            "name"      => $name,
            "currency"  => $currency,
            "id"        => $id            
        ];
        
        return $this->sendRequest($datas, 'plansApiUrl');          
    }
    
    /**
     * Create a stripe customer and/or subscribes him to a plan
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request request object
     * @param string                                    $plan    Plan to subscribe to
     * 
     * @return mixed
     */
    public function createCustomerAction(Request $request)
    {
        $stripeToken        = $request->get('stripeToken');
        $stripeUserEmail    = $request->get('stripeEmail');       
        $plan               = $request->get('plan');
        
        if (isset($plan) && !empty($plan)) {
            $datas = [
                "card"          => $stripeToken,
                "email"         => $stripeUserEmail,
                "plan"          => $plan
            ];            
        } else {
            $datas = [
                "card"          => $stripeToken,
                "email"         => $stripeUserEmail,
                "description"   => 'Useer wrapper'            
            ];            
        }

        return $this->sendRequest($datas, 'customersApiUrl');          
    }
    
    /**
     * Create a charge
     * 
     * @return mixed
     */
    private function createChargeAction()
    {
        $helper         = $this->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        
        $amount         = $this->getrequest()->get('amount');
        
        if (!empty($amount)) {
            $datas = [
                'amount'        => $amount,
                'currency'      => $setGetContract->defaultCurrency,
                'customer'      => $this->customerId,        
            ];        

            return $this->sendRequest($datas, 'chargesApiUrl');
            
        } else {
            
            return false;
        }
    }    
    
    /**
     * Send the request via cUrl
     * 
     * @param array  $datas Datas sent by url
     * @param string $type  Url type
     */
    private function sendRequest($datas, $type)
    {
        $helper         = $this->get('ek_stripe_payment.helper.stripe');
        $setGetContract = $helper->setGetContract;
        $firewall       = $helper->firewall;
        
        $curl           = $this->get('ek_api_caller.contract.http');
        
        $curl->setParameter('url',     $setGetContract->{$type});      
        $curl->setParameter('headers', $setGetContract->headers);       
        $curl->setParameter('datas',   $datas);        
        $request = $curl->post();
        
        $firewall->handleStripeError($request[0], $request[1]);
        
        if (($type === 'customersApiUrl')) {
            $this->customerId = (isset($request[0]['id'])) ? 
                    $request[0]['id'] : $this->customerId;
        }
        
        return true;
    }
    
    /**
     * Creates a new Card entity.
     *
     */
    public function createAction(Request $request)
    {
        $user   = $this->get('security.context')->getToken()->getUser();        
        $userId = $user->getId();
        
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('EkStripePaymentBundle:User')
                ->findOneByUser($userId);        
        
        if (!$entity) {
            $this->createCustomerAction($request);
            
            $entity = new User();
            $entity->setStripeUserId($this->customerId);
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();            
            
        } else {
            $this->customerId = $entity->getStripeUserId();
            $this->createCustomerAction($request);
        }
        
        $this->createChargeAction();      

        return $this->render('EkStripePaymentBundle:User:new.html.twig');
    }
}

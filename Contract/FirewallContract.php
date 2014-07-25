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

use EdouardKombo\PhpObjectsContractBundle\Contract\Elements\Abstractions\FirewallAbstractions;

use EdouardKombo\EkStripePaymentBundle\Exception\StripeException;

/**
 * EkStripePaymentBundle Firewall
 *
 * @category Contract
 * @package  EkStripePaymentBundle
 * @author   Edouard Kombo <edouard.kombo@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://creativcoders.wordpress.com
 */
class FirewallContract extends FirewallAbstractions
{   
    /**
     * Check if property exists in class
     * 
     * @param string                                                      $property Class property
     * @param \EdouardKombo\EkStripePaymentBundle\Contract\SetGetContract $class    Class object
     * 
     * @return boolean
     * @throws StripeException
     */
    public function checkIfPropertyExists($property, $class)
    {
        $message = "EkStripePayment Exception: Unknown property '$property'. ";
        $message .= "To list valid properties, open SetGetContract class"; 

        if (!isset($class->{$property})) {
            throw new StripeException($message); 
        }           
        
        return true;
    }      
    
    /**
     * Allow access on http 200 status and call exception handler otherwise
     * 
     * @param mixed   $decodedResponse Http json response
     * @param integer $code            Http status code
     * 
     * @return mixed
     */
    public function handleStripeError($decodedResponse, $code)
    {
        if (!is_array($decodedResponse) || !isset($decodedResponse['error'])) {
            return true;
        }

        $error         = $decodedResponse['error'];
        $errorMessage = isset($error['message']) ? 
                $error['message'] : 'No additional details...';
        
        return $this->httpStatusCodeAnalyzer($code, $errorMessage);
    }
    
    /**
     * Handle http error message
     * 
     * @param integer $code         Http status code
     * @param string  $errorMessage Stripe error message
     * 
     * @throws StripeException
     */
    private function httpStatusCodeAnalyzer($code, $errorMessage)
    {
        switch ($code) {           
           case 400:
               throw new StripeException('Bad request: '.$errorMessage);
           case 401:
               throw new StripeException('Unauthorized: '.$errorMessage);                 
           case 404:
               throw new StripeException('Invalid request: '.$errorMessage);
           case 401:
               throw new StripeException('Authentication error: '.$errorMessage);
           case 402:
               throw new StripeException('Card error: '.$errorMessage);
           default:
               throw new StripeException('General API Error: '.$errorMessage);
       }        
    }
    
    /**
     * Check if stripe user id is valid
     * 
     * @param mixed $user Stripe user id retrieved from database
     * 
     * @return boolean
     * @throws StripeException
     */
    public function isStripeUserValid($user)
    {
        if (false === $user) {
            throw new StripeException("Invalid Stripe User id from database !");
        }
        
        return true;
    }
}
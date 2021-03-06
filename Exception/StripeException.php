<?php

/**
 * Main docblock
 *
 * PHP version 5
 *
 * @category  Exception
 * @package   StripePaymentBundle
 * @author    Edouard Kombo <edouard.kombo@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @version   GIT: 1.0.0
 * @link      http://creativcoders.wordpress.com
 * @since     0.0.0
 */
namespace EdouardKombo\EkStripePaymentBundle\Exception;

/**
 * Customized exceptions
 *
 * @category Exception
 * @package  StripePaymentBundle
 * @author   Edouard Kombo <edouard.kombo@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://creativcoders.wordpress.com
 */
class StripeException extends \RuntimeException implements \Serializable
{
    /**
     * Serialize datas
     * 
     * @return type
     */
    public function serialize()
    {
        return serialize([
            $this->code,
            $this->message,
            $this->file,
            $this->line,
        ]);
    }

    /**
     * Unserialize datas
     * 
     * @param string $string String to unserialize
     */
    public function unserialize($string)
    {
        list(
            $this->token,
            $this->code,
            $this->message,
            $this->file,
            $this->line
        ) = unserialize($string);
    }

    /**
     * Get exception message
     * 
     * @return string
     */
    public function getMessageKey()
    {
        return (string) 'StripePaymentBundle: An exception occurred.';
    }
    
    /**
     * Get data from message
     * 
     * @return array
     */
    public function getMessageData()
    {
        return (array) array();
    }
}
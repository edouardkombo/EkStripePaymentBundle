<?php

namespace EdouardKombo\EkStripePaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="headoo_stripe_user")
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="stripe_user_id", type="string", length=255)
     */
    private $stripeUserId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\Headoo\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set stripeUserId
     *
     * @param string $stripeUserId
     * @return User
     */
    public function setStripeUserId($stripeUserId)
    {
        $this->stripeUserId = $stripeUserId;

        return $this;
    }

    /**
     * Get stripeUserId
     *
     * @return string 
     */
    public function getStripeUserId()
    {
        return $this->stripeUserId;
    }

    /**
     * Set user
     *
     * @param \Headoo\UserBundle\Entity\User $user
     * @return User
     */
    public function setUser(\Headoo\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }
}

<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;

use App\Customer\Customer;
use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Data
{


    /** @var string|null */
    protected $firstName;

    /** @var string|null */
    protected $lastName;

    /** @var string|null */
    protected $email;

    /** @var string|null */
    protected $telephone;

    /** @var string|null */
    protected $requestDate;

    /** @var string|null */
    protected $comment;

    /** @var Customer|null */
    protected $customer;

    /** @var int|null */
    protected $customerId;



    public function __construct(string $firstName = NULL,
                                string $lastName = NULL,
                                string $email = NULL,
                                string $telephone = NULL,
                                int $customerId = NULL)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->customerId = $customerId;
    }



    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }



    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }



    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }



    /**
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->telephone;
    }



    /**
     * @param $customer Customer
     * @return self
    */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @return Customer|null
    */
    public function getCustomer()
    {
        return $this->customer ?: NULL;
    }



    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param $comment string|null
     * @return self
    */
    public function setComment(string $comment) : self
    {
        $this->comment = $comment;
        return $this;
    }



    /**
     * @return string|null
    */
    public function getComment()
    {
        return $this->comment;
    }



    /**
     * @param $customer Customer
     * @return Data
     */
    public static function createFromCustomer(Customer $customer) : Data
    {
        $data = new Data($customer->getFirstName(), $customer->getLastName(), $customer->getEmail(), $customer->getTelephone(), $customer->getId());
        $data->setCustomer($customer);
        return $data;
    }



    /**
     * @param $cart ShoppingCart
     * @return Data
     */
    public static function createFromShoppingCart(ShoppingCart $cart) : Data
    {
        return new Data($cart->getFirstName(), $cart->getLastName(), $cart->getBillingEmail(), $cart->getBillingTelephone());
    }

}
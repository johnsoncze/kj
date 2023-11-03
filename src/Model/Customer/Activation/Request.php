<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

use App\Customer\Customer;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Request
{


    /** @var string */
    protected $email;

    /** @var string */
    protected $token;



    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
    }



    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }



    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }



    /**
     * @param $customer Customer
     * @return self
     * @throws \InvalidArgumentException
     */
    public static function createFromCustomer(Customer $customer) : self
    {
        $activationToken = $customer->getActivationToken();
        if ($activationToken === NULL) {
            throw new \InvalidArgumentException('Missing activation token.');
        }
        return new Request($customer->getEmail(), $customer->getActivationToken());
    }
}
<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

use App\Customer\Customer;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;
use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ActivationRequest
{


    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }



    /**
     * Set activation request on a customer.
     * @param $customer Customer
     * @return Customer
     * @throws ActivationRequestException customer is activated already
     */
    public function setNew(Customer $customer) : Customer
    {
        $token = $this->generateToken();
        $tokenValidTo = new DateTime('+3 days');

        try {
            $customer->setActivationToken($token);
            $customer->setActivationTokenValidTo($tokenValidTo->format('Y-m-d H:i:s'));
            return $customer;
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ActivationRequestException($this->translator->translate('customer.activated.already'));
        }
    }



    /**
     * Activate a customer.
     * @param $customer Customer
     * @param $password string
     * @return Customer
     * @throws ActivationRequestException on activated already or activated expired
     */
    public function activate(Customer $customer, string $password) : Customer
    {
        if ($customer->isActivated() === TRUE) {
            throw new ActivationRequestException($this->translator->translate('customer.activated.already'));
        }

        $actualDate = (new DateTime())->format('Y-m-d H:i:s');
        if ($customer->getActivationTokenValidTo(TRUE) < $actualDate) {
            throw new ActivationRequestException($this->translator->translate('customer.activation.expired'));
        }
        $customer->setPassword($password);
        $customer->setActivationToken(NULL);
        $customer->setActivationTokenValidTo(NULL);
        $customer->setActivationDate($actualDate);

        return $customer;
    }



    /**
     * Generate random activation token.
     * @return string
     */
    protected function generateToken() : string
    {
        return Random::generate(32);
    }
}
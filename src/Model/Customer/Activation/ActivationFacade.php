<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

use App\Customer\Customer;
use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\Password\PasswordService;
use Nette\Localization\ITranslator;
use Nette\Security\Passwords;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ActivationFacade
{


    /** @var ActivationRequest */
    private $activationRequest;

    /** @var ActivationSendEmail */
    private $activationSendEmail;

    /** @var CustomerRepository */
    private $customerRepo;

    /** @var PasswordService */
    private $passwordService;

    /** @var ITranslator */
    private $translator;



    public function __construct(ActivationRequest $activationRequest,
                                ActivationSendEmail $activationSendEmail,
                                CustomerRepository $customerRepository,
                                ITranslator $translator,
                                PasswordService $passwordService)
    {
        $this->activationRequest = $activationRequest;
        $this->activationSendEmail = $activationSendEmail;
        $this->customerRepo = $customerRepository;
        $this->passwordService = $passwordService;
        $this->translator = $translator;
    }



    /**
     * Activate a customer.
     * @param $email string
     * @param $token string
     * @param $password string
     * @return Customer
     * @throws ActivationFacadeException
     */
    public function activate(string $email, string $token, string $password) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedByEmailAndActivationToken($email, $token);
            $this->activationRequest->activate($customer, $this->passwordService->hash($customer, $password));
            $this->customerRepo->save($customer);
            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new ActivationFacadeException($this->translator->translate('customer.not.found'));
        } catch (ActivationRequestException $exception) {
            throw new ActivationFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $email string
     * @param $token string
     * @return Request
     * @throws ActivationFacadeException
     */
    public function validateRequest(string $email, string $token) : Request
    {
        try {
            $customer = $this->customerRepo->getOneAllowedByEmailAndActivationToken($email, $token);
            if ($customer->hasValidActivationRequest() !== TRUE) {
                throw new ActivationFacadeException('customer.activation.expired', ActivationFacadeException::EXPIRED);
            }
            return Request::createFromCustomer($customer);
        } catch (CustomerNotFoundException $exception) {
            throw new ActivationFacadeException($this->translator->translate('customer.not.found'));
        }
    }



    /**
     * Create a new activation request.
     * @param $email string
     * @return Customer
     * @throws ActivationFacadeException
     */
    public function createRequest(string $email) : Customer
    {
        try {
            $customer = $this->customerRepo->getOneAllowedByEmail($email);
            $this->activationRequest->setNew($customer);
            $this->customerRepo->save($customer);
            $this->activationSendEmail->sendRequest($customer);
            return $customer;
        } catch (CustomerNotFoundException $exception) {
            throw new ActivationFacadeException($this->translator->translate('customer.not.found'));
        } catch (ActivationRequestException $exception) {
            throw new ActivationFacadeException($exception->getMessage());
        } catch (\EntityInvalidArgumentException $exception) {
            throw new ActivationFacadeException($exception->getMessage());
        }
    }
}
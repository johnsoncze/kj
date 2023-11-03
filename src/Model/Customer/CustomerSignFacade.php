<?php

declare(strict_types = 1);

namespace App\Customer;

use App\Extensions\Nette\UserIdentity;
use App\Password\PasswordService;
use App\Password\PasswordServiceException;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CustomerSignFacade
{


    /** @var CustomerRepository */
    private $customerRepo;

    /** @var PasswordService */
    private $passwordService;

    /** @var ITranslator */
    private $translator;



    public function __construct(CustomerRepository $customerRepo,
                                ITranslator $translator,
                                PasswordService $passwordService)
    {
        $this->customerRepo = $customerRepo;
        $this->passwordService = $passwordService;
        $this->translator = $translator;
    }



    /**
     * Identify customer by email and his password.
     * @param $email string
     * @param $password string
     * @return UserIdentity|null
     * @throws CustomerSignFacadeException
     * todo test
     */
    public function identify(string $email, string $password) : UserIdentity
    {
        try {
            $customer = $this->customerRepo->getOneAllowedByEmail($email);
            if ($customer->isActivated()) {
                $this->passwordService->verify($customer, $password);
                $identity = new UserIdentity();
                $identity->setId($customer->getId());
                $identity->setEntity($customer);
                return $identity;
            }
        } catch (CustomerNotFoundException $exception) {
            //noting
        } catch (PasswordServiceException $exception) {
            //nothing
        }
        throw new CustomerSignFacadeException($this->translator->translate('form.sign.in.error.failure'));
    }
}
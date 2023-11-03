<?php

namespace App\ForgottenPassword;

use App\Customer\Customer;
use App\Facades\MailerFacade;
use App\ServiceException;
use Kdyby\Translation\ITranslator;
use App\NObject;


class ForgottenPasswordEmailService extends NObject
{


    /** @var MailerFacade */
    protected $mailerFacade;

    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator,
                                MailerFacade $mailerFacade)
    {
        $this->mailerFacade = $mailerFacade;
        $this->translator = $translator;
    }



    /**
     * Send new request e-mail for user
     * @param $entity ForgottenPasswordEntity
     * @param $email string
     * @return void
     * @throws ServiceException
     */
    public function sendNewRequestUser(ForgottenPasswordEntity $entity, $email)
    {
        if (!$email) {
            throw new ServiceException("Missing e-mail");
        }
        $this->mailerFacade->addTo($email);
        $this->mailerFacade->setSubject("Zapomenuté heslo");
        $this->mailerFacade->setTemplate("ForgottenPasswordUserRequest", [
            "entity" => $entity,
            "email" => $email
        ]);
        $this->mailerFacade->send();
    }



    /**
     * Send request to customer.
     * @param $forgottenPassword ForgottenPasswordEntity
     * @param $customer Customer
     * @return void
     */
    public function sendRequestToCustomer(ForgottenPasswordEntity $forgottenPassword, Customer $customer)
    {
        $this->mailerFacade->addTo($customer->getEmail());
        $this->mailerFacade->setSubject($this->translator->translate('customer.email.forgottenPassword.request.subject'));
        $this->mailerFacade->setTemplate('ForgottenPasswordCustomerRequest', ['forgottenPassword' => $forgottenPassword, 'customer' => $customer]);
        $this->mailerFacade->send();
    }
}
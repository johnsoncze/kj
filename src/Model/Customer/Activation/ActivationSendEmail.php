<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

use App\Customer\Customer;
use App\Facades\MailerFacade;
use Kdyby\Translation\Translator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ActivationSendEmail
{


    /** @var MailerFacade */
    private $mailerFacade;

    /** @var Translator */
    private $translator;



    public function __construct(MailerFacade $mailerFacade,
                                Translator $translator)
    {
        $this->mailerFacade = $mailerFacade;
        $this->translator = $translator;
    }



    /**
     * Send request email.
     * @param $customer Customer
     * @return void
     */
    public function sendRequest(Customer $customer)
    {
        $this->mailerFacade->addTo($customer->getEmail());
        $this->mailerFacade->setSubject($this->translator->translate('customer.email.storeRegistration.subject'));
        $this->mailerFacade->setTemplate('CustomerActivationRequest', ['customer' => $customer]);
        $this->mailerFacade->send();
    }
}
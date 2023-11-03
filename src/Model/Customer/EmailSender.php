<?php

declare(strict_types = 1);

namespace App\Customer;

use App\Facades\MailerFacade;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EmailSender
{


    /** @var MailerFacade */
    private $mailer;

    /** @var ITranslator */
    private $translator;



    public function __construct(MailerFacade $mailerFacade,
                                ITranslator $translator)
    {
        $this->mailer = $mailerFacade;
        $this->translator = $translator;
    }



    /**
     * @param $customer Customer
     * @return Customer
     */
    public function sendSuccessfulRegistration(Customer $customer)
    {
        $this->mailer->addTo($customer->getEmail());
        $this->mailer->setSubject($this->translator->translate('customer.email.registration.subject'));
        $this->mailer->setTemplate('CustomerSuccessfulRegistration');
        $this->mailer->send();
    }
}
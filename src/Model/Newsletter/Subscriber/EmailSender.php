<?php

declare(strict_types = 1);

namespace App\Newsletter\Subscriber;

use App\Facades\MailerFacade;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EmailSender
{


    /** @var MailerFacade */
    protected $mailer;

    /** @var ITranslator */
    protected $translator;



    public function __construct(ITranslator $translator,
                                MailerFacade $mailerFacade)
    {
        $this->mailer = $mailerFacade;
        $this->translator = $translator;
    }



    /**
     * Send request for confirm subscriber.
     * @param $subscriber Subscriber
     * @return Subscriber
     */
    public function sendRequest(Subscriber $subscriber) : Subscriber
    {
        $this->mailer->addTo($subscriber->getEmail());
        $this->mailer->setSubject($this->translator->translate('newsletter.subscriber.confirm.email.subject'));
        $this->mailer->setTemplate(__DIR__ . '/Emails/confirmRequest.latte', [
            'subscriber' => $subscriber,
        ]);
        $this->mailer->send();

        return $subscriber;
    }
}
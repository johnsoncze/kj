<?php

declare(strict_types = 1);

namespace App\Opportunity\Email;

use App\Email\AbstractSendEmail;
use App\Facades\MailerFacade;
use App\Opportunity\Opportunity;
use App\Opportunity\Product\Product;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\ITranslator;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EmailSender extends AbstractSendEmail
{


    /** @var Logger */
    protected $logger;

    /** @var MailerFacade */
    protected $mailer;

    /** @var ITranslator */
    protected $translator;



    public function __construct(Container $container,
                                Logger $logger,
                                ITranslator $translator,
                                MailerFacade $mailer)
    {
        parent::__construct($container);
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }



    /**
     * Send email.
     * @param $opportunity Opportunity
     * @param $products Product[]|array
     * @return Opportunity
     */
    public function send(Opportunity $opportunity, array $products = []) : Opportunity
    {
      $notificationEmailCategory = $opportunity->isType(Opportunity::TYPE_CONTACT_FORM) ? self::NOTIFICATION_EMAIL_CONTACT_FORM_CATEGORY : self::NOTIFICATION_EMAIL_DEMAND_CATEGORY;
      $notificationEmails = $this->getNotificationEmails($notificationEmailCategory);

      if($opportunity->getEmail()){
        $this->mailer->addTo($opportunity->getEmail());
        $notificationEmails && $this->mailer->addBcc($notificationEmails);
      }
      else { //when no client email is given
        $this->mailer->addTo($notificationEmails);
      }
        try {
            $template = sprintf(__DIR__ . '/Templates/%s', $opportunity->getEmailTemplateName());
            $this->mailer->setSubject($this->translator->translate($opportunity->getEmailSubjectTranslationKey()));
            $this->mailer->setTemplate($template, ['opportunity' => $opportunity, 'products' => $products]);
            $this->mailer->send();
        } catch (\EntityInvalidArgumentException $exception) {
            $message = sprintf('An error has been occurred on sending confirmation email of opportunity. Error: %s', $exception->getMessage());
            $this->logger->addError($message, ['opportunity' => $opportunity]);
        }

        return $opportunity;
    }

}

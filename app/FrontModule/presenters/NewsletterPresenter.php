<?php

declare(strict_types = 1);

namespace App\FrontModule\Presenters;

use App\Newsletter\Subscriber\SubscriberFacadeFactory;
use App\Newsletter\SubscriberFacadeException;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use App\FrontModule\Components\Ecomail\EcomailHelper;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class NewsletterPresenter extends AbstractPresenter
{

    /** @var SubscriberFacadeFactory @inject */
    public $subscriberFacadeFactory;

    /** @var EcomailHelper @inject */
    public $ecomailHelper;

    /**
     * @param $email string
     * @param $token string
     * @return void
     * @throws BadRequestException
     * @throws AbortException
     */
    public function actionConfirmSubscription(string $email, string $token)
    {
        try {
            $this->database->beginTransaction();
            $subscriberFacade = $this->subscriberFacadeFactory->create();
            $subscriberFacade->confirm($email, $token);
            $this->database->commit();

            //send to ecomail
            //$this->ecomailHelper->addNewsletterSubscribe($email);

            $this->flashMessage($this->translator->translate('form.newsletter.subscription.confirm.message.success'), 'success');
            $this->redirect('Homepage:default');
        } catch (SubscriberFacadeException $exception) {
            $this->database->rollBack();
            throw new BadRequestException(NULL, 404);
        }
    }
}
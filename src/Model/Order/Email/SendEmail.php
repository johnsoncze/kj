<?php

declare(strict_types = 1);

namespace App\Order\Email;

use App\Email\AbstractSendEmail;
use App\Facades\MailerFacade;
use App\Order\Order;
use Kdyby\Translation\ITranslator;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SendEmail extends AbstractSendEmail
{


    /** @var MailerFacade */
    protected $mailerFacade;

    /** @var ITranslator */
    protected $translator;



    public function __construct(Container $container,
                                ITranslator $translator,
                                MailerFacade $mailerFacade)
    {
        parent::__construct($container);
        $this->translator = $translator;
        $this->mailerFacade = $mailerFacade;
    }



    /**
     * Send confirmation of sent order.
     * @param $order Order
     * @return Order
     */
    public function confirmation(Order $order) : Order
    {
        $notificationEmails = $this->getNotificationEmails(self::NOTIFICATION_EMAIL_ORDER_CATEGORY);

        $this->mailerFacade->addTo($order->getCustomerEmail());
        $notificationEmails && $this->mailerFacade->addBcc($notificationEmails);
        $this->mailerFacade->setSubject($this->translator->translate('order.email.confirmation.subject', ['code' => $order->getCode()]));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/confirmation.latte', ['order' => $order]);
		//$this->mailerFacade->addAttachedFiles("/www/files/obchodni_podminky.pdf");
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * Send readyForPickUp email.
     * @param $order Order
     * @return Order
     */
    public function readyForPickUp(Order $order) : Order
    {
        $this->mailerFacade->addTo($order->getCustomerEmail());
        $this->mailerFacade->setSubject($this->translator->translate('order.email.readyforpickup.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/readyforpickup.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * Send email that order was sent.
     * @param $order Order
     * @return Order
     */
    public function sent(Order $order) : Order
    {
        $this->mailerFacade->addTo($order->getCustomerEmail());
        $this->mailerFacade->setSubject($this->translator->translate('order.email.sent.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/sent.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * Send email that order was cancelled.
     * @param $order Order
     * @return Order
     */
    public function cancelled(Order $order) : Order
    {
        $notificationEmails = $this->getNotificationEmails(self::NOTIFICATION_EMAIL_ORDER_CATEGORY);

        $this->mailerFacade->addTo($order->getCustomerEmail());
        $notificationEmails && $this->mailerFacade->addBcc($notificationEmails);
        $this->mailerFacade->setSubject($this->translator->translate('order.email.cancelled.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/cancelled.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * Send email that order was stopped.
     * @param $order Order
     * @return Order
     */
    public function stopped(Order $order) : Order
    {
        $this->mailerFacade->addTo($order->getCustomerEmail());
        $this->mailerFacade->setSubject($this->translator->translate('order.email.stopped.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/stopped.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * @param $order Order
     * @return Order
     */
    public function paymentGatewayPaid(Order $order): Order
    {
        $this->mailerFacade->addTo($order->getCustomerEmail());
        $this->mailerFacade->setSubject($this->translator->translate('order.email.paymentGateway.paid.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/paymentGatewayPaid.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }



    /**
     * @param $order Order
     * @return Order
     */
    public function paymentGatewayCancelled(Order $order): Order
    {
        $this->mailerFacade->addTo($order->getCustomerEmail());
        $this->mailerFacade->setSubject($this->translator->translate('order.email.paymentGateway.cancelled.subject'));
        $this->mailerFacade->setTemplate(__DIR__ . '/Templates/paymentGatewayCancelled.latte', ['order' => $order]);
        $this->mailerFacade->send();

        return $order;
    }
}
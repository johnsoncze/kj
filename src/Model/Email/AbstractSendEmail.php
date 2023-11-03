<?php

declare(strict_types = 1);

namespace App\Email;

use App\Facades\MailerFacade;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractSendEmail
{


    /** @var string */
    const NOTIFICATION_EMAIL_DEMAND_CATEGORY = 'demand';
    const NOTIFICATION_EMAIL_ORDER_CATEGORY = 'order';
    const NOTIFICATION_EMAIL_CONTACT_FORM_CATEGORY = 'contactForm';

    /** @var Container */
    protected $container;



    public function __construct(Container $container)
    {
        $this->container = $container;
    }



    /**
     * @param $category string
     * @return array
     */
    protected function getNotificationEmails(string $category) : array
    {
        $parameters = $this->container->getParameters();
        return $parameters['notification']['email'][$category] ?? [];
    }
}
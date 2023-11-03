<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Newsletter\SubscriberList\SubscriberList;
use App\AdminModule\Components\Newsletter\SubscriberList\SubscriberListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class NewsletterPresenter extends AdminModulePresenter
{


    /** @var SubscriberListFactory @inject */
    public $subscriberListFactory;



    /**
     * @return SubscriberList
     */
    public function createComponentSubscriberList() : SubscriberList
    {
        return $this->subscriberListFactory->create();
    }
}
<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Newsletter\SubscriberList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SubscriberListFactory
{


    /**
     * @return SubscriberList
     */
    public function create();
}
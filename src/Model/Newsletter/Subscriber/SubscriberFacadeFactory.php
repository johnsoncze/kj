<?php


declare(strict_types = 1);

namespace App\Newsletter\Subscriber;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SubscriberFacadeFactory
{


    /**
     * @return SubscriberFacade
     */
    public function create();
}
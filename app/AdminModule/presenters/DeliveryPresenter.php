<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\DeliveryList\DeliveryList;
use App\AdminModule\Components\DeliveryList\DeliveryListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeliveryPresenter extends AdminModulePresenter
{


    /** @var DeliveryListFactory @inject */
    public $deliveryListFactory;



    /**
     * @return DeliveryList
     */
    public function createComponentDeliveryList() : DeliveryList
    {
        return $this->deliveryListFactory->create();
    }
}
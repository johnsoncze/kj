<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\Diamond\PriceList\PriceList;
use App\AdminModule\Components\Diamond\PriceList\PriceListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class DiamondPresenter extends AdminModulePresenter
{


    /** @var PriceListFactory @inject */
    public $priceListFactory;



    /**
     * @return PriceList
     */
    public function createComponentPriceList() : PriceList
    {
        return $this->priceListFactory->create();
    }
}
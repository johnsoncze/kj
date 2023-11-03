<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\ProductStateList\ProductStateList;
use App\AdminModule\Components\ProductStateList\ProductStateListFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @breadcrumb-nav-parent :Admin:Product:default
 */
class ProductStatePresenter extends AdminModulePresenter
{


    /** @var ProductStateListFactory @inject */
    public $productStateListFactory;



    /**
     * @return ProductStateList
     */
    public function createComponentProductStateList() : ProductStateList
    {
        return $this->productStateListFactory->create();
    }
}
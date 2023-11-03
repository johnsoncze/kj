<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Information;

use App\Product\ProductDTO;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use App\ProductParameterGroup\Lock\Parameter\Parameter;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Information extends Control
{


    /** @var LockFacade */
    private $lockFacade;

    /** @var ProductDTO|null */
    private $product;



    public function __construct(LockFacadeFactory $lockFacade)
    {
        parent::__construct();
        $this->lockFacade = $lockFacade->create();
    }



    /**
     * @param $product ProductDTO
     * @return self
     */
    public function setProduct(ProductDTO $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render()
    {
    	$product = $this->product->getProduct();

        $this->template->parameters = $this->getProductParameterList($this->product);
        $this->template->product = $product;
        $this->template->showGoldInfo = in_array(Parameter::QUALITY_VALUE, $this->lockFacade->findByKeyAndProductId(Lock::PRODUCT_DETAIL_JK_QUALITY, $product->getId()), TRUE);

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $product ProductDTO
	 * @return array
     */
    private function getProductParameterList(ProductDTO $product) : array
    {
        return $product->getProductParameterList();
    }
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\BenefitList;

use App\Product\Product;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use App\ProductParameterGroup\Lock\Parameter\Parameter;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class BenefitList extends Control
{


    /** @var LockFacade */
    private $lockFacade;

    /** @var Product|null */
    private $product;



    public function __construct(LockFacadeFactory $lockFacadeFactory)
    {
        parent::__construct();
        $this->lockFacade = $lockFacadeFactory->create();
    }



    /**
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render()
    {
        $this->template->benefits = $this->getBenefits($this->product);
        $this->template->product = $this->product;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $product Product
     * @return Benefit[]
     */
    private function getBenefits(Product $product) : array
    {
        $lockParameters = $this->lockFacade->findByKeyAndProductId(Lock::PRODUCT_DETAIL_BENEFIT, $product->getId());

        //relation parameters
        if (in_array(Parameter::WARRANTY_JK_VALUE, $lockParameters, TRUE)) {
            $benefits[] = new Benefit('star', 'product.benefit.service.title', 'product.benefit.service.description');
        }
//        if (in_array(Parameter::RING_SIZE_ADJUSTMENT_VALUE, $lockParameters, TRUE)) {
//            $benefits[] = new Benefit('ruler', 'product.benefit.size.adjustment.title', 'product.benefit.size.adjustment.description');
//        }
        $benefits[] = new Benefit('truck', 'product.benefit.delivery.title', 'product.benefit.delivery.description');
        $benefits[] = new Benefit('recycle', 'product.benefit.exchange.title', 'product.benefit.exchange.description');
        $benefits[] = new Benefit('store', 'product.benefit.shop.title', 'product.benefit.shop.description');

/*        if (in_array(Parameter::WEEDING_RING_DISCOUNT, $lockParameters, TRUE)) {
			$benefits[] = new Benefit('discount', 'category.template.default.benefit.weedingRingDiscount.title', 'category.template.default.benefit.weedingRingDiscount.description');
		}*/

        return $benefits;
    }
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Order\RelatedProduct;

use App\Customer\Customer;
use App\FrontModule\Components\Product\ProductList\ProductList;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\Helpers\Entities;
use App\Order\Order;
use App\Product\ProductDTO;
use App\Product\ProductFindFacadeFactory;
use App\Product\Related\Related;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class RelatedProduct extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var Order|null */
    private $order;

    /** @var ProductFindFacadeFactory */
    private $productFindFacadeFactory;

    /** @var ProductListFactory */
    private $productListFactory;



    public function __construct(ProductFindFacadeFactory $productFindFacadeFactory,
                                ProductListFactory $productListFactory)
    {
        parent::__construct();
        $this->productFindFacadeFactory = $productFindFacadeFactory;
        $this->productListFactory = $productListFactory;
    }



    /**
     * @param $customer Customer
     * @return self
     */
    public function setCustomer(Customer $customer) : self
    {
        $this->customer = $customer;
        return $this;
    }



    /**
     * @param $order Order
     * @return self
     */
    public function setOrder(Order $order) : self
    {
        $this->order = $order;
        return $this;
    }



    /**
     * @return ProductList
     */
    public function createComponentProductList() : ProductList
    {
        $productList = $this->productListFactory->create();
        $this->customer ? $productList->setCustomer($this->customer) : NULL;
        $productList->setProducts($this->getProducts());

        return $productList;
    }



    public function render()
    {
        $this->template->products = $this->getProducts();
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return ProductDTO[]|array
     */
    private function getProducts() : array
    {
        static $products = [];
        if (!$products) {
            $productId = Entities::getProperty($this->order->getProducts(), 'productId');
            $productFindFacade = $this->productFindFacadeFactory->create();
            $products = $productFindFacade->findPublishedRelatedProductsByMoreProductIdAndType($productId, Related::SET_TYPE);
        }
        return $products;
    }
}
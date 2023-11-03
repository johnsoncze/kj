<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\CrossSelling;

use App\Customer\Customer;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\Helpers\Entities;
use App\Product\ProductDTO;
use App\Product\ProductFindFacadeFactory;
use App\Product\Related\Related;
use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\ShoppingCartDTO;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductList extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var ProductFindFacadeFactory */
    private $productFindFacadeFactory;

    /** @var ProductListFactory */
    private $productListFactory;

    /** @var ShoppingCartProduct|null */
    private $shoppingCartProduct;

    /** @var ProductDTO[]|array */
    private $relatedProducts = [];

    /** @var ShoppingCartDTO|null */
    private $shoppingCart;



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
     * @param $shoppingCartDTO ShoppingCartDTO
     * @param $shoppingCartProduct ShoppingCartProduct|null if is inserted, will be show related products only for the product
     * @return self
     */
    public function setShoppingCart(ShoppingCartDTO $shoppingCartDTO, ShoppingCartProduct $shoppingCartProduct = NULL) : self
    {
        $this->shoppingCart = $shoppingCartDTO;
        $this->shoppingCartProduct = $shoppingCartProduct;
        $this->relatedProducts = $this->getProducts();
        return $this;
    }



    /**
     * @return \App\FrontModule\Components\Product\ProductList\ProductList
     */
    public function createComponentProductList() : \App\FrontModule\Components\Product\ProductList\ProductList
    {
        $list = $this->productListFactory->create();
        $list->setProducts($this->relatedProducts);
        $this->customer ? $list->setCustomer($this->customer) : NULL;
        return $list;
    }



    public function render()
    {
        $this->template->products = $this->relatedProducts;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return ProductDTO[]|array
     */
    private function getProducts() : array
    {
        $_products = [];
        $cartProducts = $this->shoppingCart->getProducts();
        if ($cartProducts) {
            $catalogProductId = $this->shoppingCartProduct ? [$this->shoppingCartProduct->getProductId()] : Entities::getProperty($cartProducts, 'productId');
            $productFindFacade = $this->productFindFacadeFactory->create();
            $products = $productFindFacade->findPublishedRelatedProductsByMoreProductIdAndType($catalogProductId, Related::CROSS_SELLING);
            $_products = array_diff_key($products, Entities::setValueAsKey($cartProducts, 'productId')); //not show products which are in shopping cart already
        }
        return $_products;
    }
}
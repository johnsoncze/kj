<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\RelatedList;

use App\Customer\Customer;
use App\FrontModule\Components\Product\ProductList\ProductList;
use App\FrontModule\Components\Product\ProductList\ProductListFactory;
use App\Product\Product;
use App\Product\ProductDTO;
use App\Product\ProductFindFacadeFactory;
use App\Product\Related\Related;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class RelatedList extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var Product|null */
    private $product;

    /** @var ProductFindFacadeFactory */
    private $productFindFacadeFactory;

    /** @var ProductDTO[]|array */
    private $productsDTO;

    /** @var ProductListFactory */
    private $productListFactory;

    /** @var mixed */
    private $type = Related::SET_TYPE;



    public function __construct(ProductFindFacadeFactory $productFindFacadeFactory,
								ProductListFactory $productListFactory)
    {
        parent::__construct();
        $this->productFindFacadeFactory = $productFindFacadeFactory;
        $this->productListFactory = $productListFactory;
    }



	/**
	 * @param mixed $type
	 * @return self
	 */
	public function setType($type) : self
	{
		$this->type = $type;
		return $this;
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
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        $this->productsDTO = $this->getProducts($product);
        return $this;
    }



    /**
     * @return ProductList
     */
    public function createComponentProductList() : ProductList
    {
        $list = $this->productListFactory->create();
        $list->setProducts($this->productsDTO);
        $this->customer ? $list->setCustomer($this->customer) : NULL;
        return $list;
    }



    public function render()
    {
        $this->template->products = $this->productsDTO;
        $this->template->type = $this->type;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    public function renderSlider()
	{
		$this->template->products = $this->productsDTO;
		$this->template->type = $this->type;

		$this->template->setFile(__DIR__ . '/slider.latte');
		$this->template->render();
	}
    public function renderSimilar()
	{
		$this->template->products = $this->productsDTO;
		$this->template->type = $this->type;

		$this->template->setFile(__DIR__ . '/similar.latte');
		$this->template->render();
	}    public function renderRelated()
	{
		$this->template->products = $this->productsDTO;
		$this->template->type = $this->type;

		$this->template->setFile(__DIR__ . '/related.latte');
		$this->template->render();
	}



    /**
     * @param $product Product
     * @return array|ProductDTO[]
     */
    private function getProducts(Product $product) : array
    {
    	$productFindFacade = $this->productFindFacadeFactory->create();
    	return $productFindFacade->findPublishedRelatedProductsByMoreProductIdAndType([$product->getId()], (string)$this->type);
    }
}
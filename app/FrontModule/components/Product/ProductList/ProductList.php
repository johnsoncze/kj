<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductList;

use App\Category\CategoryEntity;
use App\Customer\Customer;
use App\FrontModule\Components\Product\Preview\PreviewFactory;
use App\Product\ProductDTO;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use App\FrontModule\Components\Store\OpeningHours\OpeningHours;
use App\FrontModule\Components\Store\OpeningHours\OpeningHoursFactory;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductList extends Control
{


    /** @var Customer|null */
    private $customer;

    /** @var PreviewFactory */
    private $productPreviewFactory;

    /** @var ProductDTO[]|array */
    private $products = [];

    /** @var OpeningHoursFactory */
    private $openingHoursFactory;
		

    public function __construct(PreviewFactory $previewFactory,
																OpeningHoursFactory $openingHoursFactory)
    {
        parent::__construct();
        $this->productPreviewFactory = $previewFactory;
        $this->openingHoursFactory = $openingHoursFactory;
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
     * @param $products ProductDTO[]
     * @return self
     */
    public function setProducts(array $products) : self
    {
    	$this->products = $products;
    	return $this;
    }



    public function render()
    {
    	$this->createProductComponents();

        $this->template->customer = $this->customer;
        $this->template->products = $this->products;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



	public function renderJewellery(CategoryEntity $category)
	{
		$this->createProductComponents();

		$this->template->customer = $this->customer;
		$this->template->products = $this->products;
		$this->template->category = $category;

		$this->template->setFile(__DIR__ . '/jewellery.latte');
		$this->template->render();
	}



    public function renderMore()
	{
		$this->createProductComponents();

		$this->template->customer = $this->customer;
		$this->template->products = $this->products;

		$this->template->setFile(__DIR__ . '/more.latte');
		$this->template->render();
	}



	/**
	 * @param $products ProductDTO[]|array
	 * @throws InvalidStateException
	*/
	public function renderCollectionImageTemplateRow(array $products)
	{
		$this->setProducts($products);
		$this->createProductComponents();

		$this->template->customer = $this->customer;
		$this->template->products = $this->products;

		$this->template->setFile(__DIR__ . '/collectionImageTemplateRow.latte');
		$this->template->render();
	}



	/**
	 * @return void
	 * @throws InvalidStateException
	*/
	private function createProductComponents()
	{
		$products  = $this->products ?: [];
		foreach ($products as $product) {
			$name = 'product_' . $product->getProduct()->getId();
			$preview = $this->productPreviewFactory->create();
			$preview->setProduct($product);
			$this->customer ? $preview->setCustomer($this->customer) : NULL;
			$this->addComponent($preview, $name);
		}
	}
	
    /**
     * @return OpeningHours
     */
    public function createComponentOpeningHours() : OpeningHours
    {
        return $this->openingHoursFactory->create();
    }	
	
}
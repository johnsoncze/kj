<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Preview;

use App\Customer\Customer;
use App\Product\ProductDTO;
use Nette\Application\UI\Control;
use App\FrontModule\Components\Favourite\ProductHeart\ProductHeart;
use App\FrontModule\Components\Favourite\ProductHeart\ProductHeartFactory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Preview extends Control
{

	/** @var ProductHeartFactory */
	private $productHeartFactory;	
	
	/** @var Customer|null */
	private $customer;

    /** @var ProductDTO|null */
    private $product;



	public function __construct(ProductHeartFactory $productHeartFactory)
	{
		parent::__construct();
		$this->productHeartFactory = $productHeartFactory;
	}
	
		
	/**
	 * @return ProductHeart
	 */
	public function createComponentFavouriteProductHeart() : ProductHeart
	{
		return $this->productHeartFactory->create();
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
     * @param $product ProductDTO
     * @return self
     */
    public function setProduct(ProductDTO $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render($lazy = false)
    {
        $this->template->lazy = $lazy;
			 	$this->template->customer = $this->customer;
        $this->template->product = $this->product;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
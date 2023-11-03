<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\OpportunityForm;

use App\Product\ProductDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Product
{


	/** @var ProductDTO */
	protected $product;

	/** @var int */
	protected $quantity;



	public function __construct(ProductDTO $product, int $quantity = 1)
	{
		$this->product = $product;
		$this->quantity = $quantity;
	}



	/**
	 * @return ProductDTO
	 */
	public function getProduct() : ProductDTO
	{
		return $this->product;
	}



	/**
	 * @return int
	 */
	public function getQuantity() : int
	{
		return $this->quantity;
	}
}
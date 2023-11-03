<?php

declare(strict_types = 1);

namespace App\Product;

use App\Product\Variant\VariantRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductMasterFinder
{


	/** @var ProductRepository */
	protected $productRepo;

	/** @var VariantRepository */
	protected $variantRepo;



	public function __construct(ProductRepository $productRepo,
								VariantRepository $variantRepo)
	{
		$this->productRepo = $productRepo;
		$this->variantRepo = $variantRepo;
	}



	/**
	 * @param $id int
	 * @return Product|null
	 * @throws ProductNotFoundException
	 */
	public function findOneByProductId(int $id)
	{
		$variant = $this->variantRepo->findOneByProductVariantId($id);
		return $variant ? $this->productRepo->getOneById($variant->getProductId()) : NULL;
	}
}
<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait AddedTrait
{


	/**
	 * @param $product Product
	 * @return int
	 */
	private function getAddedSorting(Product $product) : int
	{
		$dateObject = new \DateTime($product->getAddDate());
		$sorting = (int)$dateObject->format('YmdHis');
		unset($dateObject); //remove object from memory

		return $sorting;
	}
}
<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait NoveltyTrait
{


	/**
	 * @param $product Product
	 * @return int
	 */
	private function getNoveltySorting(Product $product) : int
	{
		if ($product->isNew()) {
			$sorting = BaseSorter::SORT_FORWARD;
			$actualDateObject = new \DateTime();
			$addedDateObject = new \DateTime($product->getAddDate());
			$dateDiff = $actualDateObject->diff($addedDateObject);
			$sorting .= $dateDiff->format('%Y%M%D%H%I%S');
			unset($actualDateObject, $addedDateObject, $dateDiff); //remove objects from memory
		} else {
			//zeros replaced date diff of case if product is new
			$sorting = BaseSorter::SORT_REARWARD . '000000000000';
		}


		return (int)$sorting;
	}
}
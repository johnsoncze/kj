<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait StockTrait
{


	/**
	 * @param $product Product
	 * @return int
	 */
	protected function getStockSorting(Product $product) : int
	{
		return $product->isInStock() ? BaseSorter::SORT_FORWARD : BaseSorter::SORT_REARWARD;
	}
}
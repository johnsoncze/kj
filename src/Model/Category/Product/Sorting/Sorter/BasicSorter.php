<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\Product\Sorting\Sorting;
use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class BasicSorter extends BaseSorter
{


	use AddedTrait;
	use NoveltyTrait;
	use StockTrait;



	/**
	 * @inheritdoc
	 */
	protected function resolveSorting(Product $product) : Sorting
	{
		$noveltySorting = $this->getNoveltySorting($product);
		$stockSorting = $this->getStockSorting($product);
		$addedSorting = $this->getAddedSorting($product);

		$sorting = $this->createSortingHash($noveltySorting, $stockSorting, $addedSorting);
		return $this->createSortingObject($product, $sorting);
	}

}
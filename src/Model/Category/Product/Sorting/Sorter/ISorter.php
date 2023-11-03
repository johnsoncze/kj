<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\CategoryEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ISorter
{


	/**
	 * @param $category CategoryEntity
	 * @return void
	 */
	public function execute(CategoryEntity $category);
}
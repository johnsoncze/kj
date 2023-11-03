<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParentVariantList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParentVariantListFactory
{


	/**
	 * @return ProductParentVariantList
	 */
	public function create();
}
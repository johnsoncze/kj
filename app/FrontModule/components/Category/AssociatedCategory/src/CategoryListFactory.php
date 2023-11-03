<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\AssociatedCategory;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryListFactory
{


	/**
	 * @return CategoryList
	 */
	public function create();
}
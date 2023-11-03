<?php

declare(strict_types = 1);

namespace App\Category\AssociatedCategory;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFacadeFactory
{


	/**
	 * @return CategoryFacade
	 */
	public function create();
}
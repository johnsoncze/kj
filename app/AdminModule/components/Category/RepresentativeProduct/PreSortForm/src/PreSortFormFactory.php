<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\RepresentativeProduct\PreSortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PreSortFormFactory
{


	/**
	 * @return PreSortForm
	 */
	public function create();
}
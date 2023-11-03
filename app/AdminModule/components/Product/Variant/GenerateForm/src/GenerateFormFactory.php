<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\Variant\GenerateForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface GenerateFormFactory
{


	/**
	 * @return GenerateForm
	 */
	public function create();
}
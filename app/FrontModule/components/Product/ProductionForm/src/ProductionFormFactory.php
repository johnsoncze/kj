<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductionForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductionFormFactory
{


	/**
	 * @return ProductionForm
	 */
	public function create();
}
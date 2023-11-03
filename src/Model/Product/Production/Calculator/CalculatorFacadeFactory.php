<?php

declare(strict_types = 1);

namespace App\Product\Production\Calculator;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CalculatorFacadeFactory
{


	/**
	 * @return CalculatorFacade
	 */
	public function create();
}
<?php

declare(strict_types = 1);

namespace App\Tests\ProductState;

use App\ProductState\ProductState;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductStateTestTrait
{


	/**
	 * @return ProductState
	 */
	private function createTestProductState() : ProductState
	{
		$state = new ProductState();
		$state->setProduction(FALSE);
		$state->setSort(1);

		return $state;
	}
}
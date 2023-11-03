<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\Map;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface MapFactory
{


	/**
	 * @return Map
	 */
	public function create();
}
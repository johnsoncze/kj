<?php

declare(strict_types = 1);

namespace App\Product\Brand;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Brand
{


	/** @var string */
	protected $name;



	public function __construct(string $name)
	{
		$this->name = $name;
	}



	/**
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}
}
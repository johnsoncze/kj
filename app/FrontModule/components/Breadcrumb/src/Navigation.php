<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Breadcrumb;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Navigation
{


	/** @var array */
	protected $items = [];



	/**
	 * @param $item Item
	 * @return self
	 */
	public function addItem(Item $item) : self
	{
		$this->items[] = $item;
		return $this;
	}



	/**
	 * @return Item[]|array
	 */
	public function getItems() : array
	{
		return $this->items;
	}

}
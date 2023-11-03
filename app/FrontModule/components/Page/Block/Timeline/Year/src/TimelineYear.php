<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\Timeline\Year;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class TimelineYear extends Control
{


	/** @var Item[]|array */
	private $items = [];



	/**
	 * @param $item Item
	 * @return self
	 */
	public function addItem(Item $item) : self
	{
		$this->items[] = $item;
		return $this;
	}



	public function render()
	{
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->items = $this->items;
		$this->template->render();
	}
}
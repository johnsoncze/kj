<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Breadcrumb;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Breadcrumb extends Control
{


	/** @var Navigation|null */
	private $navigation;



	/**
	 * @param $navigation Navigation
	 * @return self
	 */
	public function setNavigation(Navigation $navigation) : self
	{
		$this->navigation = $navigation;
		return $this;
	}



	public function render()
	{
		$this->template->navigation = $this->navigation;
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->render();
	}



	public function renderWhite()
    {
        $this->template->style = 'Breadcrumb--white';
        $this->render();
    }
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Articles;

use Nette\Application\UI\Control;


final class Articles extends Control
{

	public function __construct()
	{
		parent::__construct();
	}


	public function renderVariant1()
	{
		$this->template->setFile(__DIR__ . '/variant1.latte');
		$this->template->render();
	}

	
	public function renderVariant2()
	{
		$this->template->setFile(__DIR__ . '/variant2.latte');
		$this->template->render();
	}
	
}
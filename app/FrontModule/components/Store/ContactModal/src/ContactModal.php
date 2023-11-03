<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\ContactModal;

use App\FrontModule\Components\Store\Map\Map;
use App\FrontModule\Components\Store\Map\MapFactory;
use App\FrontModule\Components\Store\OpeningHours\OpeningHours;
use App\FrontModule\Components\Store\OpeningHours\OpeningHoursFactory;
use Nette\Application\UI\Control;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ContactModal extends Control
{


	/** @var MapFactory */
	private $mapFactory;

	/** @var OpeningHoursFactory */
	private $openingHoursFactory;

	/** @var array */
	private $parameters;



	public function __construct(Container $container,
								MapFactory $mapFactory,
								OpeningHoursFactory $openingHoursFactory)
	{
		parent::__construct();
		$this->mapFactory = $mapFactory;
		$this->openingHoursFactory = $openingHoursFactory;
		$this->parameters = $container->getParameters();
	}



	/**
	 * @return Map
	 */
	public function createComponentMap() : Map
	{
		return $this->mapFactory->create();
	}



	/**
	 * @return OpeningHours
	 */
	public function createComponentOpeningHours() : OpeningHours
	{
		return $this->openingHoursFactory->create();
	}



	public function render()
	{
		$this->template->parameters = $this->parameters;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}
}
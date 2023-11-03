<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\Map;

use Kdyby\Monolog\Logger;
use Nette\Application\UI\Control;
use Nette\DI\Container;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Map extends Control
{


	/** @var Logger */
	private $logger;

	/** @var array */
	private $parameters;



	public function __construct(Container $container,
								Logger $logger)
	{
		parent::__construct();
		$this->logger = $logger;
		$this->parameters = $container->getParameters();
	}



	public function render()
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');
		$this->template->mapParameters = $this->getMapParameters();
		$this->template->render();
	}



	/**
	 * @return array
	 */
	private function getMapParameters() : array
	{
		$street = $this->parameters['project']['address'] ?? NULL;
		$apiKey = $this->parameters['googleApi']['key'] ?? NULL;

		if ($street === NULL || $apiKey === NULL) {
			$this->logger->addNotice('Missing some require parameters for map.', $this->parameters);
		}

		return [
			'street' => $street,
			'apiKey' => $apiKey,
		];
	}
}
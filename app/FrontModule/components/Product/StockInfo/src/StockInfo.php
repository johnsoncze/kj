<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\StockInfo;

use App\FrontModule\Components\Store\ContactModal\ContactModal;
use App\FrontModule\Components\Store\ContactModal\ContactModalFactory;
use App\Product\ProductDTO;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class StockInfo extends Control
{


	/** @var ContactModalFactory */
	private $contactModalFactory;

	/** @var ProductDTO|null */
	private $product;



	public function __construct(ContactModalFactory $contactModalFactory)
	{
		parent::__construct();
		$this->contactModalFactory = $contactModalFactory;
	}



	/**
	 * @return ContactModal
	 */
	public function createComponentContactModal() : ContactModal
	{
		return $this->contactModalFactory->create();
	}



	/**
	 * @param $product ProductDTO
	 * @return self
	 */
	public function setProduct(ProductDTO $product) : self
	{
		$this->product = $product;
		return $this;
	}



	public function render()
	{
		$this->template->product = $this->product;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}
}
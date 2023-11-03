<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\MetaSmallBlock;

use App\Customer\Customer;
use App\Product\Product;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use App\ProductParameterGroup\Lock\Parameter\Parameter;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class MetaSmallBlock extends Control
{


	/** @var Customer|null */
	private $customer;

	/** @var LockFacadeFactory */
	private $lockFacadeFactory;

	/** @var ITranslator */
	private $translator;

	/** @var Product|null */
	private $product;



	public function __construct(LockFacadeFactory $lockFacadeFactory, ITranslator $translator)
	{
		parent::__construct();
		$this->lockFacadeFactory = $lockFacadeFactory;
        $this->translator = $translator;
	}



	/**
	 * @param $customer Customer
	 * @return self
	 */
	public function setCustomer(Customer $customer) : self
	{
		$this->customer = $customer;
		return $this;
	}



	/**
	 * @param $product Product
	 * @return self
	 */
	public function setProduct(Product $product) : self
	{
		$this->product = $product;
		return $this;
	}



	public function render()
	{
		$lockFacade = $this->lockFacadeFactory->create();
		$parameters = $lockFacade->findByKeyAndProductId(Lock::PRODUCT_DETAIL_BENEFIT, $this->product->getId());

		$this->template->customer = $this->customer;
		$this->template->hasWeedingRingDiscount = in_array(Parameter::WEEDING_RING_DISCOUNT, $parameters, TRUE);
		$this->template->loggedCustomerDiscountRate = $this->presenter->loggedCustomerDiscountRate;
		$this->template->loggedCustomerBirthdayDiscountRate = $this->presenter->loggedCustomerBirthdayDiscountRate;
		$this->template->product = $this->product;
        $this->template->translator = $this->translator;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}
}
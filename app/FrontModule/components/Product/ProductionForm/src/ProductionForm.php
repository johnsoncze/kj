<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\ProductionForm;

use App\Customer\Customer;
use App\FrontModule\Components\Product\ProductionTimeForm\FormContainer;
use App\FrontModule\Components\Product\ProductionTimeForm\FormContainerFactory;
use App\Helpers\Prices;
use App\Product\ProductDTO;
use App\Product\Production\Calculator\CalculatorFacadeException;
use App\Product\Production\Calculator\CalculatorFacadeFactory;
use App\Product\Production\Time\Time;
use Kdyby\Monolog\Logger;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductionForm extends Control
{

	/** @var string http post value name */
	const PRODUCTION_TIME_KEY = 'productionTime';

	/** @var string */
	const SUBMIT_BUTTON_ID = 'productionFormButton';

	/** @var Customer|null */
	private $customer;

	/** @var Logger */
	private $logger;

	/** @var ProductDTO|null */
	private $product;

	/** @var CalculatorFacadeFactory */
	private $calculatorFacadeFactory;

	/** @var FormContainerFactory */
	private $timeForm;



	public function __construct(CalculatorFacadeFactory $calculatorFacadeFactory, FormContainerFactory $formContainerFactory, Logger $logger)
	{
		parent::__construct();
		$this->calculatorFacadeFactory = $calculatorFacadeFactory;
		$this->logger = $logger;
		$this->timeForm = $formContainerFactory;
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
	 * @return Form
	 */
	public function createComponentForm() : Form
	{
		$productionTimeForm = $this->timeForm->create();
		$productionTimeForm->getComponent('productionTime')->setAttribute('class', 'calculate-production js-selectfield select2-hidden-accessible');

		$form = new Form();
		$form->addComponent($productionTimeForm, FormContainer::NAME);
		$form->addSubmit('submit')
			->setAttribute('hidden', TRUE)
			->setHtmlId(self::SUBMIT_BUTTON_ID);
		$form->onSuccess[] = [$this, 'formSuccess'];

		return $form;
	}



	public function render()
	{
		$this->template->product = $this->product;
		$this->template->setFile(__DIR__ . '/default.latte');
		$this->template->render();
	}



	/**
	 * @param $form Form
	 * @throws AbortException
	 */
	public function formSuccess(Form $form)
	{
		$values = $form->getValues();
		$presenter = $this->getPresenter();

		$params['productionTime'] = $values->{FormContainer::NAME}->productionTime;
		$presenter->redirect('addToShoppingCart!', $params);
	}



	/**
	 * Ajax handler for calculate product price.
	 * @return void
	 * @throws AbortException
	*/
	public function handleCalculate()
	{
		$presenter = $this->getPresenter();
		if ($presenter->isAjax()) {
			$response['code'] = 0;
			$productionTime = $this->getParameter(self::PRODUCTION_TIME_KEY);
			$productionTimeId = Time::resolveId($productionTime);
			$customerId = $this->customer ? $this->customer->getId() : NULL;

			try {
				$calculatorFacade = $this->calculatorFacadeFactory->create();
				$price = $calculatorFacade->calculate($this->product->getProduct()->getId(), $productionTimeId, $customerId);

				$response['price'] = Prices::toUserFriendlyFormat($this->product->getProduct()->isDiscountAllowed() ? $price->summary : $price->summaryBeforeDiscount);
				$response['priceBeforeDiscount'] = Prices::toUserFriendlyFormat($price->summaryBeforeDiscount);
			} catch (CalculatorFacadeException $exception) {
				$response['code'] = 500;

				$message = sprintf('An error has been occurred on calculate price of production. Error: %s', $exception->getMessage());
				$this->logger->addWarning($message, [
					'customer' => $this->customer,
					'product' => $this->product,
					'productionTimeId' => $productionTimeId,
				]);
			}

			$presenter->sendJson($response);
		}
	}
}
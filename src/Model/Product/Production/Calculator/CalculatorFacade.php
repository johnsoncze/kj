<?php

declare(strict_types = 1);

namespace App\Product\Production\Calculator;

use App\Customer\CustomerNotFoundException;
use App\Customer\CustomerRepository;
use App\NotFoundException;
use App\Price\Price;
use App\Product\Production\ProductionTimeDTO;
use App\Product\Production\Time\TimeRepository;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepository;
use Kdyby\Translation\Translator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CalculatorFacade
{


	/** @var Calculator */
	private $calculator;

	/** @var CustomerRepository */
	private $customerRepo;

	/** @var ProductPublishedRepository */
	private $productRepo;

	/** @var TimeRepository */
	private $productionTimeRepo;

	/** @var Translator */
	private $translator;



	/**
	 * CalculatorFacade constructor.
	 * @param $calculator Calculator
	 * @param $customerRepo
	 * @param ProductPublishedRepository $productRepo
	 * @param TimeRepository $productionTimeRepo
	 * @param $translator Translator
	 */
	public function __construct(Calculator $calculator, CustomerRepository $customerRepo, ProductPublishedRepository $productRepo, TimeRepository $productionTimeRepo, Translator $translator)
	{
		$this->calculator = $calculator;
		$this->customerRepo = $customerRepo;
		$this->productRepo = $productRepo;
		$this->productionTimeRepo = $productionTimeRepo;
		$this->translator = $translator;
	}



	/**
	 * @param $productId int
	 * @param $productionTimeId int
	 * @param $customerId int|null
	 * @return Price
	 * @throws CalculatorFacadeException
	 * todo test
	 */
	public function calculate(int $productId, int $productionTimeId, int $customerId = NULL) : Price
	{
		try {
			$product = $this->productRepo->getOneById($productId, $this->translator);
			$productionTime = $this->productionTimeRepo->getOnePublishedById($productionTimeId);
			$customer = $customerId !== NULL ? $this->customerRepo->getOneAllowedById($customerId) : NULL;
			$basePrice = new \App\Product\Price\Base\Price((float)$product->getPrice(), (float)$product->getVat());
			$productionTimeDTO = new ProductionTimeDTO('workaround', 'workaround', (float)$productionTime->getSurcharge());

			return $this->calculator->calculate($customer, $basePrice, $productionTimeDTO);
		} catch (ProductNotFoundException $exception) {
			throw new CalculatorFacadeException($exception->getMessage());
		} catch (NotFoundException $exception) {
			throw new CalculatorFacadeException($exception->getMessage());
		} catch (CustomerNotFoundException $exception) {
			throw new CalculatorFacadeException($exception->getMessage());
		} catch (\InvalidArgumentException $exception) {
			throw new CalculatorFacadeException($exception->getMessage());
		}
	}
}
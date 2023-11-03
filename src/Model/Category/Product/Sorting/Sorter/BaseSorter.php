<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\CategoryEntity;
use App\Category\Product\Sorting\Sorting;
use App\Category\Product\Sorting\SortingRepository;
use App\CategoryProductParameter\CategoryProductParameterEntity;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\Helpers\Entities;
use App\Product\Product;
use App\Product\ProductRepository;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseSorter implements ISorter
{


	/** @var string */
	const LOGGER_NAMESPACE = 'category.product.sorting';

	/** @var int */
	const SORT_FORWARD = 1;
	const SORT_REARWARD = 2;

	/** @var CategoryEntity */
	protected $category;

	/** @var CategoryProductParameterRepository */
	private $categoryParameterRepo;

	/** @var Logger */
	protected $logger;

	/** @var ProductRepository */
	private $productRepo;

	/** @var SortingRepository */
	private $sortingRepo;



	/**
	 * BaseSorter constructor.
	 * @param CategoryProductParameterRepository $categoryParameterRepo
	 * @param Logger $logger
	 * @param ProductRepository $productRepo
	 * @param SortingRepository $sortingRepo
	 */
	public function __construct(CategoryProductParameterRepository $categoryParameterRepo,
								Logger $logger,
								ProductRepository $productRepo,
								SortingRepository $sortingRepo)
	{
		$this->categoryParameterRepo = $categoryParameterRepo;
		$this->logger = $logger;
		$this->productRepo = $productRepo;
		$this->sortingRepo = $sortingRepo;
	}



	/**
	 * @inheritdoc
	 */
	public function execute(CategoryEntity $category)
	{
		$this->category = $category;
		$categoryParameters = $this->findCategoryParameters($category);
		if ($categoryParameters) {
			$productsId = $this->findProductIdByCategoryParameters($categoryParameters);
			if ($productsId) {
                $this->productBatching($productsId);
            }
		}
	}



	/**
	 * Resolve product sorting.
	 * @param $product Product
	 * @return Sorting
	 */
	abstract protected function resolveSorting(Product $product) : Sorting;



	/**
	 * @param $category CategoryEntity
	 * @return CategoryProductParameterEntity[]|array
	 */
	private function findCategoryParameters(CategoryEntity $category) : array
	{
		return $this->categoryParameterRepo->findByCategoryId($category->getId());
	}



	/**
	 * @param $parameters CategoryProductParameterEntity[]
	 * @return array
	 */
	private function findProductIdByCategoryParameters(array $parameters) : array
	{
		$parameterId = Entities::getProperty($parameters, 'productParameterId');
		return $this->productRepo->findProductIdByMoreParameterIdAsCategoryParameter($parameterId);
	}



	/**
	 * @param $productsId int[]
	 * @param $batch int
	 * @return void
	 */
	private function productBatching(array $productsId, int $batch = 100)
	{
		$productCount = count($productsId);
		for ($i = 0; $i <= $productCount; $i += $batch) {
			$sortingBatch = [];
			$productsIdBatch = array_slice($productsId, $i, $batch);
			if (!$productsIdBatch) {
			    continue;
            }

			$products = $this->productRepo->findByMoreId($productsIdBatch);
			foreach ($products as $product) {
				$sortingBatch[] = $this->resolveSorting($product);
			}
			$this->saveSortingBatch($sortingBatch, $productsIdBatch, $this->category);
			$this->logResolveSortingBatch($sortingBatch);
			unset($product, $sortingBatch);
		}
	}



	/**
	 * @param $sorting Sorting[]
	 * @return void
	 */
	private function logResolveSortingBatch(array $sorting)
	{
		foreach ($sorting as $s) {
			$this->logResolvedSorting($s);
		}
	}



	/**
	 * @param $sorting Sorting
	 * @return void
	 */
	private function logResolvedSorting(Sorting $sorting)
	{
		$message = $this->createLogMessage(sprintf('Bylo vypočítáno řazení pro produkt \'%d\' v kategorii \'%d\'. Výsledek: %s', $sorting->getProductId(), $sorting->getId(), $sorting->getSorting()));

		//commented because capacity of logging system
		//$this->logger->addInfo($message);
	}



	/**
	 * @param $message string
	 * @return string
	 */
	private function createLogMessage(string $message) : string
	{
		return sprintf('%s: %s', self::LOGGER_NAMESPACE, $message);
	}



	/**
	 * @param $sorting Sorting[]
	 * @param $productsId array
	 * @param $category CategoryEntity
	 * @return void
	 */
	private function saveSortingBatch(array $sorting, array $productsId, CategoryEntity $category)
	{
		$this->sortingRepo->deleteByMoreProductIdAndCategoryId($productsId, $category->getId());
		$this->sortingRepo->save($sorting);
	}



	/**
	 * @param $product Product
	 * @param $sorting string
	 * @return Sorting
	 */
	protected function createSortingObject(Product $product, string $sorting) : Sorting
	{
		$sortingObject = new Sorting();
		$sortingObject->setProductId($product->getId());
		$sortingObject->setCategoryId($this->category->getId());
		$sortingObject->setSorting($sorting);

		return $sortingObject;
	}



	/**
	 * Create sorting hash in format xxx-xxx-xxx-xxx
	 * @param $sorting array
	 * @return string
	 */
	protected function createSortingHash(...$sorting) : string
	{
		return implode('-', $sorting);
	}



	/**
	 * @param $maxSortingLength int length of code
	 * @return int
	 */
	protected function toEnd(int $maxSortingLength = 5) : int
	{
		return (int)str_repeat('9', $maxSortingLength);
	}
}
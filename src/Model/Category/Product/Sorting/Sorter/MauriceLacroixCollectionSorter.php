<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\Product\Sorting\Sorting;
use App\Category\Product\Sorting\SortingRepository;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\Product\Product;
use App\Product\ProductRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacade;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class MauriceLacroixCollectionSorter extends BaseSorter
{


	use NoveltyTrait;

	/** @var LockFacade */
	private $groupParameterLockFacade;



	public function __construct(CategoryProductParameterRepository $categoryParameterRepo,
								LockFacadeFactory $lockFacadeFactory,
								Logger $logger,
								ProductRepository $productRepo,
								SortingRepository $sortingRepo)
	{
		parent::__construct($categoryParameterRepo, $logger, $productRepo, $sortingRepo);
		$this->groupParameterLockFacade = $lockFacadeFactory->create();
	}



	/**
	 * @inheritdoc
	 */
	protected function resolveSorting(Product $product) : Sorting
	{
		$noveltySorting = $this->getNoveltySorting($product);
		$genderSorting = $this->getGenderSorting($product);
		$similarModelSorting = $this->getSimilarModelSorting($product);

		$sorting = $this->createSortingHash($noveltySorting, $genderSorting, $similarModelSorting);
		return $this->createSortingObject($product, $sorting);
	}



	/**
	 * @param $product Product
	 * @return int
	 */
	private function getGenderSorting(Product $product) : int
	{
		$parameter = $this->groupParameterLockFacade->findOneValueByKeyAndProductId(Lock::WATCH_GENDER, $product->getId());
		if (!$parameter) {
			$message = sprintf('Pro hodinky ML s id \'%d\' chybí parametr o určení.', $product->getId());
			$this->logger->addNotice($message);
		}

		return $parameter ? (int)$parameter : $this->toEnd(1);
	}



	/**
	 * @param $product Product
	 * @return int
	 */
	private function getSimilarModelSorting(Product $product) : int
	{
		preg_match('/^([A-Za-z]+)(\d{4,9})-(.+)-(.+)$/', $product->getCode(), $matched);
		if (!$matched) {
			$message = sprintf('Nebylo možné zjistit číslo modelu pro žazení u hodinek ML s id \'%s\'. Produkt obsahuje neznámý kód \'%s\'.', $product->getId(), $product->getCode());
			$this->logger->addNotice($message);
			return 0;
		}
		list(, , $model, , ,) = $matched;

		//because models are going to descending sorting
		//and sorting number must be ascendant
		return str_repeat('9', 9) - $model;
	}

}
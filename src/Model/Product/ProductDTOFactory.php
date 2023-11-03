<?php

declare(strict_types = 1);

namespace App\Product;

use App\Category\CategoryFindFacadeFactory;
use App\Category\CategoryRepository;
use App\Helpers\Entities;
use App\Product\Brand\Brand;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameterGroup\Lock\Lock;
use App\ProductParameterGroup\Lock\LockFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\Product\Parameter\ProductParameterRepository AS ProductParameterRelationRepository;
use App\ProductState\ProductStateRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductDTOFactory
{

	/** @var CategoryFindFacadeFactory */
	protected $categoryFindFacadeFactory;

	/** @var CategoryRepository */
	protected $categoryRepo;

	/** @var LockFacadeFactory */
	protected $lockFacadeFactory;

	/** @var ProductParameterGroupRepository */
	protected $productParameterGroupRepo;

	/** @var ProductParameterRelationRepository */
	protected $productParameterRelationRepo;

	/** @var ProductParameterRepository */
	protected $productParameterRepo;

	/** @var ProductStateRepository */
	protected $stateRepo;



	public function __construct(CategoryFindFacadeFactory $categoryFindFacadeFactory,
								CategoryRepository $categoryRepository,
								LockFacadeFactory $lockFacadeFactory,
								ProductParameterGroupRepository $productParameterGroupRepository,
								ProductParameterRelationRepository $productParameterRelationRepository,
								ProductParameterRepository $productParameterRepository,
								ProductStateRepository $productStateRepository)
	{
		$this->categoryFindFacadeFactory = $categoryFindFacadeFactory;
		$this->categoryRepo = $categoryRepository;
		$this->lockFacadeFactory = $lockFacadeFactory;
		$this->productParameterGroupRepo = $productParameterGroupRepository;
		$this->productParameterRelationRepo = $productParameterRelationRepository;
		$this->productParameterRepo = $productParameterRepository;
		$this->stateRepo = $productStateRepository;
	}



	/**
	 * Create from products.
	 * @param $products Product[]
	 * @param $brand bool
	 * @param $category bool
	 * @return array
	 */
	public function createFromProducts(array $products, bool $brand = FALSE, bool $category = FALSE) : array
	{
		$productsDTO = [];
		$productId = Entities::getProperty($products, 'id');

		//get brands
		$lockFacade = $this->lockFacadeFactory->create();
		$brands = $brand ? $lockFacade->getByKeyAndMoreProductId(Lock::EE_TRACKING_BRAND, $productId) : [];

		//get categories
		$categories = $category ? $lockFacade->getByKeyAndMoreProductId(Lock::PRODUCT_MAIN_CATEGORY, $productId) : [];
		$categoryObjects = $categories ? $this->categoryRepo->findPublishedByMoreId(Entities::getProperty($categories, 'value')) : [];

		//get states
		$stateId = Entities::getValueFromMethod($products, 'getStockStateByStockQuantity');
		$states = $this->stateRepo->findByMoreId($stateId);
		$states = Entities::setIdAsKey($states);

		//get parameters
		$parameters = $this->getParameters($productId);

		foreach ($products as $product) {
			$brand = $brands[$product->getId()] ?? NULL;
			$category = $categories[$product->getId()] ?? NULL;
			$categoryObject = $category && isset($categoryObjects[$category->getValue()]) ? $categoryObjects[$category->getValue()] : NULL;
			$state = $states[$product->getStockStateByStockQuantity()];
			$productDTO = new ProductDTO($product, $state, $brand ? new Brand($brand->getValue()) : NULL, $categoryObject);
			isset($parameters[$product->getId()]) ? $productDTO->setParameters($parameters[$product->getId()]) : NULL;
			$productsDTO[$product->getId()] = $productDTO;
		}
		return $productsDTO;
	}



	/**
	 * @param $productId array
	 * @return array|ProductParameterEntity[][]
	 */
	private function getParameters(array $productId) : array
	{
		$returnParameters = [];
		$productParameters = $this->productParameterRelationRepo->findByMoreProductId($productId);
		if ($productParameters) {
			$parameters = $this->productParameterRepo->findByMoreId(Entities::getProperty($productParameters, 'parameterId'));
			$groups = $this->productParameterGroupRepo->findByMoreId(Entities::getProperty($parameters, 'productParameterGroupId'));
			foreach ($productParameters as $productParameter) {
				$parameter = $parameters[$productParameter->getParameterId()];
				$group = $groups[$parameter->getProductParameterGroupId()];
				$parameter->setGroup($group);
				$returnParameters[$productParameter->getProductId()][] = $parameter;
			}
		}
		return $returnParameters;
	}
}
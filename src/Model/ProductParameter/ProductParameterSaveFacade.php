<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSaveFacade extends NObject
{


	/** @var ProductParameterRepositoryFactory */
	protected $productParameterRepositoryFactory;



	public function __construct(ProductParameterRepositoryFactory $productParameterRepositoryFactory)
	{
		$this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
	}



	/**
	 * @param ProductParameterGroupEntity $parameterGroupEntity
	 * @param $helperId int|null
	 * @return ProductParameterEntity
	 * todo remove and use ::save() method
	 */
	public function add(ProductParameterGroupEntity $parameterGroupEntity, int $helperId = NULL) : ProductParameterEntity
	{
		//Create entity
		$entityFactory = new ProductParameterEntityFactory();
		$entity = $entityFactory->create($parameterGroupEntity->getId());
		$entity->setHelperId($helperId);

		//Repo
		$repo = $this->productParameterRepositoryFactory->create();

		//Save
		$repo->save($entity);

		return $entity;
	}



	/**
	 * @param $parameterId int|null
	 * @param $helperId int|null
	 * @return ProductParameterEntity
	 * @throws ProductParameterSaveFacadeException
	 * todo test
	 */
	public function save(int $parameterId = NULL,
						 int $helperId = NULL) : ProductParameterEntity
	{
		try {
			$parameterRepo = $this->productParameterRepositoryFactory->create();
			$parameter = $parameterId !== NULL ? $parameterRepo->getOneById($parameterId) : new ProductParameterEntity();
			$parameter->setHelperId($helperId);
			$parameterRepo->save($parameter);
			return $parameter;
		} catch (ProductParameterNotFoundException $exception) {
			throw new ProductParameterSaveFacadeException();
		}
	}
}
<?php

declare(strict_types = 1);

namespace App\Product;

use App\Category\CategoryEntity;
use App\Product\Brand\Brand;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductState\ProductState;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductDTO
{

	/** @var Brand|null */
	protected $brand;

	/** @var CategoryEntity|null */
	protected $category;

	/** @var Product */
	protected $product;

	/** @var array|ProductParameterEntity[] */
	protected $productParameters = [];

	/** @var ProductState */
	protected $state;



	public function __construct(Product $product,
								ProductState $productState,
								Brand $brand = NULL,
								CategoryEntity $category = NULL)
	{
		$this->brand = $brand;
		$this->category = $category;
		$this->product = $product;
		$this->state = $productState;
	}



	/**
	 * @param $parameters ProductParameterEntity[]
	 * @return self
	 */
	public function setParameters(array $parameters) : self
	{
		$this->productParameters = $parameters;
		return $this;
	}



	/**
	 * @return Brand|null
	*/
	public function getBrand()
	{
		return $this->brand;
	}



	/**
	 * @return CategoryEntity|null
	*/
	public function getCategory()
	{
		return $this->category;
	}



	/**
	 * @return Product
	 */
	public function getProduct() : Product
	{
		return $this->product;
	}



	/**
	 * @return ProductState
	 */
	public function getState() : ProductState
	{
		return $this->state;
	}



	/**
	 * @return array|ProductParameterEntity[]
	 */
	public function getParameters() : array
	{
		return $this->productParameters;
	}



	/**
	 * @return ProductParameterEntity[]|array
	 */
	public function getVisibleParameters() : array
	{
		$visibleParameters = [];
		$parameters = $this->getParameters();
		foreach ($parameters as $parameter) {
			if ($parameter->getGroup()->isVisibleInOrder() === TRUE) {
				$visibleParameters[] = $parameter;
			}
		}
		return $visibleParameters;
	}



    public function getProductParameterList() : array
    {
        $parameterList = [];
        $productParameters = $this->getParameters();

        foreach ($productParameters as $productParameter) {
            /** @var $group ProductParameterGroupEntity */
            $group = $productParameter->getGroup();
            if ($group->isSeenOnProductDetail() === TRUE) {
                $key = $group->getSort() . '_' . $group->getId();
                $parameterList[$key]['group'] = $group;
                $parameterList[$key]['parameters'][] = $productParameter;
            }
        }

        ksort($parameterList, SORT_NATURAL);

        return $parameterList;
    }
}
<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\Helpers\Entities;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterFindFacade extends NObject
{


    /** @var CategoryFiltrationRepositoryFactory */
    protected $categoryFiltrationRepositoryFactory;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;



    /**
     * ProductParameterFindFacade constructor.
     * @param CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory
     * @param $productParameterRepositoryFactory ProductParameterRepositoryFactory
     */
    public function __construct(CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * @param int $categoryId
     * @return null|ProductParameterEntity[]
     */
    public function findParametersOfFiltrationByCategoryId(int $categoryId)
    {
        //find filtration of category
        $categoryFiltrationRepo = $this->categoryFiltrationRepositoryFactory->create();
        $filtration = $categoryFiltrationRepo->findByCategoryId($categoryId);
        if ($filtration) {
            $productParameterGroupsId = Entities::getProperty($filtration, "productParameterGroupId");

            //find all parameters
            $productParameterRepo = $this->productParameterRepositoryFactory->create();
            $parameters = $productParameterRepo->findByProductParameterGroupsId($productParameterGroupsId);

            if ($parameters) {
                return $parameters;
            }
        }
        return NULL;
    }
}
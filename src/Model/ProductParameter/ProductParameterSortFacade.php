<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSortFacade extends NObject
{


    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;



    /**
     * ProductParameterSortFacade constructor.
     * @param ProductParameterRepositoryFactory $productParameterRepositoryFactory
     */
    public function __construct(ProductParameterRepositoryFactory $productParameterRepositoryFactory)
    {
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
    }



    /**
     * @param $groupEntity ProductParameterGroupEntity
     * @param $sorts array [sort => productParameterId,..]
     * @return ProductParameterEntity[]
     * @throws ProductParameterSortFacadeException
     */
    public function saveSort(ProductParameterGroupEntity $groupEntity, array $sorts) : array
    {
        $parameterRepo = $this->productParameterRepositoryFactory->create();
        $parameters = $parameterRepo->findBy(array_keys($sorts));

        if (!$parameters) {
            throw new ProductParameterSortFacadeException(sprintf('Žádný parameter skupiny s id \'%s\' pro řazení.',
                $groupEntity->getId()));
        }

        $productParameterSetSort = new ProductParameterSetSort();
        foreach ($sorts as $sort => $productParameterId){
                if(!isset($parameters[$productParameterId])){
                    throw new ProductParameterSortFacadeException(sprintf('Nenalezen parameter s id \'%s\' pro řazení.', $productParameterId));
                }
                $productParameterSetSort->set($parameters[$productParameterId], (int)$sort);
        }

        //save
        $parameterRepo->save($parameters);

        return $parameters;
    }
}
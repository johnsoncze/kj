<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupSaveFacade
{


    /**
     * @var ProductParameterGroupRepositoryFactory
     */
    protected $productParameterGroupRepositoryFactory;



    public function __construct(ProductParameterGroupRepositoryFactory $productParameterGroupRepositoryFactory)
    {
        $this->productParameterGroupRepositoryFactory = $productParameterGroupRepositoryFactory;
    }



    /**
     * @param $groupId int|null
     * @param $variantType string
     * @param $filtrationType string
     * @param $visibleInOrder bool
	 * @param $visibleOnProductDetail bool
	 * @param $sort int
     * @return ProductParameterGroupEntity
     * @throws ProductParameterGroupSaveFacadeException
     */
    public function save(int $groupId = NULL,
						 string $variantType,
						 string $filtrationType,
						 bool $visibleInOrder = FALSE,
						 bool $visibleOnProductDetail = TRUE,
						 int $sort = 1) : ProductParameterGroupEntity
    {
        $groupRepo = $this->productParameterGroupRepositoryFactory->create();

        try {
            $group = $groupId !== NULL ? $groupRepo->getOneById($groupId) : new ProductParameterGroupEntity();
            $group->setVariantType($variantType);
            $group->setFiltrationType($filtrationType);
            $group->setVisibleInOrder($visibleInOrder);
            $group->setVisibleOnProductDetail($visibleOnProductDetail);
            $group->setSort($sort);
            $groupRepo->save($group);
            return $group;
        } catch (ProductParameterGroupNotFoundException $exception) {
            throw new ProductParameterGroupSaveFacadeException($exception->getMessage());
        }
    }


}
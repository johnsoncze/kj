<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\Category\CategoryEntity;
use App\Category\CategoryFiltrationRepository;
use App\ProductParameterGroup\ProductParameterGroupNotFoundException;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationSaveFacade extends NObject
{


    /**
     * @var CategoryFiltrationRepositoryFactory
     */
    protected $categoryFiltrationRepositoryFactory;

    /** @var ProductParameterGroupRepositoryFactory */
    protected $productParameterGroupRepositoryFactory;



    public function __construct(CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory,
                                ProductParameterGroupRepositoryFactory $productParameterGroupRepositoryFactory)
    {
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
        $this->productParameterGroupRepositoryFactory = $productParameterGroupRepositoryFactory;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param int $productParameterGroup
     * @param bool $index
     * @param bool $follow
     * @param bool $siteMap
     * @return CategoryFiltrationEntity
     * @throws CategoryFiltrationSaveFacadeException
     */
    public function add(CategoryEntity $categoryEntity, int $productParameterGroup,
                        bool $index = NULL, bool $follow = NULL, bool $siteMap = NULL
    ) : CategoryFiltrationEntity
    {
        try {
            //load product parameter group
            $productParameterGroupRepo = $this->productParameterGroupRepositoryFactory->create();
            $group = $productParameterGroupRepo->getOneById($productParameterGroup);

            //repo
            $repo = $this->categoryFiltrationRepositoryFactory->create();

            //create entity
            $filtrationFactory = new CategoryFiltrationEntityFactory();
            $filtration = $filtrationFactory->create($categoryEntity->getId(), $group->getId(),
                $index, $follow, $siteMap);

            //check duplicate
            $this->checkDuplicate($filtration, $repo);

            //save
            $repo->save($filtration);

            return $filtration;

        } catch (ProductParameterGroupNotFoundException $exception) {
            throw new CategoryFiltrationSaveFacadeException($exception->getMessage());
        } catch (CategoryFiltrationCheckDuplicateException $exception) {
            throw new CategoryFiltrationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param CategoryFiltrationEntity $categoryFiltrationEntity
     * @return CategoryFiltrationEntity
     */
    public function update(CategoryFiltrationEntity $categoryFiltrationEntity)
    : CategoryFiltrationEntity
    {
        //repo
        $repo = $this->categoryFiltrationRepositoryFactory->create();

        //save
        $repo->save($categoryFiltrationEntity);

        return $categoryFiltrationEntity;
    }



    /**
     * @param CategoryFiltrationEntity $filtrationEntity
     * @param CategoryFiltrationRepository $repository
     */
    protected function checkDuplicate(CategoryFiltrationEntity $filtrationEntity,
                                      CategoryFiltrationRepository $repository)
    {
        //load duplicate
        $categoryId = $filtrationEntity->getCategoryId();
        $productParameterGroupId = $filtrationEntity->getProductParameterGroupId();
        $duplicate = $repository->findOneByCategoryIdAndProductParameterGroupId($categoryId, $productParameterGroupId);

        //Check
        $checker = new CategoryFiltrationCheckDuplicate();
        $checker->check($filtrationEntity, $duplicate);
    }
}
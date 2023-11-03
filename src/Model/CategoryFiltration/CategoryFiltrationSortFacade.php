<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\Category\CategoryEntity;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationSortFacade extends NObject
{


    /**
     * @var CategoryFiltrationRepositoryFactory
     */
    protected $categoryFiltrationRepositoryFactory;



    /**
     * CategoryFiltrationSortFacade constructor.
     * @param CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory
     */
    public function __construct(CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory)
    {
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @param array $filtration [sort[int] => categoryFiltrationId[int],..]
     * @return CategoryFiltrationEntity[]
     * @throws CategoryFiltrationSortFacadeException
     */
    public function sort(CategoryEntity $categoryEntity, array $filtration) : array
    {
        //load entities
        $repo = $this->categoryFiltrationRepositoryFactory->create();
        $entities = $repo->findByCategoryId($categoryEntity->getId());

        $setter = new CategoryFiltrationSetSort();
        foreach ($filtration as $sort => $id) {
            if (!isset($entities[$id])) {
                throw new CategoryFiltrationSortFacadeException(sprintf("Unknown filtration with id '%s' for category id '%s'.", $id, $categoryEntity->getId()));
            }
            if (!is_int($sort)) {
                throw new CategoryFiltrationSortFacadeException(sprintf("You must set int as type of sort. Type '%s' given.", gettype($sort)));
            }
            $setter->setSort($entities[$id], $sort);
        }

        //save
        $repo->save($entities);

        return $entities;
    }
}
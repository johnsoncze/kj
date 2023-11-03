<?php

declare(strict_types = 1);

namespace App\Category;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryRemoveFacade extends NObject
{


    /**
     * @var CategoryRepositoryFactory
     */
    protected $categoryRepositoryFactory;



    public function __construct(CategoryRepositoryFactory $categoryRepositoryFactory)
    {
        $this->categoryRepositoryFactory = $categoryRepositoryFactory;
    }



    /**
     * @param int $categoryId
     * @throws CategoryRemoveFacadeException
     */
    public function remove(int $categoryId)
    {
        try {
            //repo
            $repo = $this->categoryRepositoryFactory->create();

            //load
            $category = $repo->getOneById($categoryId);

            //remove
            $repo->remove($category);
        } catch (CategoryNotFoundException $exception) {
            throw new CategoryRemoveFacadeException($exception->getMessage());
        }
    }
}
<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupRemoveFacade extends NObject
{


    /** @var CategoryFiltrationGroupRepositoryFactory */
    protected $categoryFiltrationGroupRepositoryFactory;



    /**
     * CategoryFiltrationGroupSaveFacade constructor.
     * @param $categoryFiltrationGroupRepositoryFactory CategoryFiltrationGroupRepositoryFactory
     */
    public function __construct(CategoryFiltrationGroupRepositoryFactory $categoryFiltrationGroupRepositoryFactory)
    {
        $this->categoryFiltrationGroupRepositoryFactory = $categoryFiltrationGroupRepositoryFactory;
    }



    /**
     * @param int $id
     * @return bool
     * @throws CategoryFiltrationGroupRemoveFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            $repo = $this->categoryFiltrationGroupRepositoryFactory->create();
            $entity = $repo->getOneById($id);
            $repo->remove($entity);
            return TRUE;
        } catch (CategoryFiltrationGroupNotFoundException $exception) {
            throw new CategoryFiltrationGroupRemoveFacadeException($exception->getMessage());
        }
    }
}
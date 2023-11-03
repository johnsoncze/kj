<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;


use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationRemoveFacade extends NObject
{


    /**
     * @var CategoryFiltrationRepositoryFactory
     */
    protected $categoryFiltrationRepositoryFactory;

    /** @var callable[]|null */
    public $onRemove;



    /**
     * CategoryFiltrationRemoveFacade constructor.
     * @param CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory
     */
    public function __construct(CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory)
    {
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
    }



    /**
     * @param int $categoryFiltrationId
     * @return CategoryFiltrationEntity
     * @throws CategoryFiltrationNotFoundException
     */
    public function remove(int $categoryFiltrationId)
    {
        try {
            //repo
            $repo = $this->categoryFiltrationRepositoryFactory->create();

            //load
            $filtration = $repo->getOneById($categoryFiltrationId);

            $this->onRemove($filtration);

            //remove
            $repo->remove($filtration);

            return $filtration;
        } catch (CategoryFiltrationNotFoundException $exception) {
            throw new CategoryFiltrationNotFoundException($exception->getMessage());
        }
    }


}
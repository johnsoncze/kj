<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\SubCategoryList;

use App\Category\CategoryEntity;
use App\Category\CategoryFindFacadeFactory;
use App\Category\CategoryRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SubCategoryList extends Control
{


    /** @var CategoryEntity|null */
    private $category;

    /** @var CategoryFiltrationGroupRepository */
    private $categoryFiltrationGroupRepo;

    /** @var CategoryFindFacadeFactory */
    private $categoryFindFacadeFactory;

    /** @var CategoryRepository */
    private $categoryRepo;




    public function __construct(CategoryFiltrationGroupRepository $categoryFiltrationGroupRepo,
                                CategoryFindFacadeFactory $categoryFindFacadeFactory,
                                CategoryRepository $categoryRepo)
    {
        parent::__construct();
        $this->categoryFiltrationGroupRepo = $categoryFiltrationGroupRepo;
        $this->categoryFindFacadeFactory = $categoryFindFacadeFactory;
        $this->categoryRepo = $categoryRepo;
    }



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setCategory(CategoryEntity $category) : self
    {
        $this->category = $category;
        return $this;
    }



    /**
     * @return void
     */
    public function render()
    {
        $this->template->category = $this->category;
        $this->template->categories = array_merge($this->getAssociatedCategories(), $this->getSubCategories($this->category));
        $this->template->groups = $this->getCategoryParameterGroups($this->category);

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $category CategoryEntity
     * @return CategoryFiltrationGroupEntity[]|array
     */
    private function getCategoryParameterGroups(CategoryEntity $category) : array
    {
        return $this->categoryFiltrationGroupRepo->findByCategoryId($category->getId()) ?: [];
    }



    /**
     * @param $category CategoryEntity
     * @return CategoryEntity[]|array
     */
    private function getSubCategories(CategoryEntity $category) : array
    {
        return $this->categoryRepo->findPublishedByParentId($category->getId());
    }



    /**
     * @return CategoryEntity[]|array
    */
    private function getAssociatedCategories() : array
    {
        $categoryFindFacade = $this->categoryFindFacadeFactory->create();
        return $categoryFindFacade->findAssociatedCategoriesById($this->category->getId());
    }
}
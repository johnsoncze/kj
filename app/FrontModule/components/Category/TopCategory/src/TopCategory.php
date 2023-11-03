<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\TopCategory;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class TopCategory extends Control
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var CategoryEntity|null */
    private $parentCategory;



    public function __construct(CategoryRepository $categoryRepo)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepo;
    }



    /**
     * @param $category CategoryEntity
     * @return self
     */
    public function setParentCategory(CategoryEntity $category) : self
    {
        $this->parentCategory = $category;
        return $this;
    }



    /**
     * @return void
     */
    public function renderTopCollection()
    {
        $topCategories = $this->getTopCategories();
        $this->template->collection = $topCategories ? end($topCategories) : NULL;
        $this->template->setFile(__DIR__ . '/templates/topCollection.latte');
        $this->template->render();
    }



    /**
     * @return CategoryEntity[]|array
     * @throws \InvalidArgumentException
     */
    private function getTopCategories() : array
    {
        $parentCategory = $this->parentCategory;
        if ($parentCategory === NULL) {
            throw new \InvalidArgumentException('Missing parent category.');
        }
        return $this->categoryRepo->findTopPublishedByParentId($parentCategory->getId());
    }
}
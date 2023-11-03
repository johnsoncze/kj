<?php

declare(strict_types = 1);

namespace App\Components\CategoryFiltrationSortForm;

use App\Category\CategoryEntity;
use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\Components\SortForm\SortForm;
use App\Components\SortForm\SortFormFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationSortForm extends Control
{


    /** @var SortFormFactory */
    protected $sortFormFactory;

    /** @var CategoryFiltrationRepositoryFactory */
    protected $categoryFiltrationRepositoryFactory;

    /** @var CategoryFiltrationSortFormFactory */
    protected $categoryFiltrationSortFormSuccessFactory;

    /** @var CategoryEntity|null */
    protected $categoryEntity;



    public function __construct(SortFormFactory $sortFormFactory,
                                CategoryFiltrationRepositoryFactory $categoryFiltrationRepositoryFactory,
                                CategoryFiltrationSortFormSuccessFactory $categoryFiltrationSortFormSuccessFactory)
    {
        parent::__construct();
        $this->sortFormFactory = $sortFormFactory;
        $this->categoryFiltrationRepositoryFactory = $categoryFiltrationRepositoryFactory;
        $this->categoryFiltrationSortFormSuccessFactory = $categoryFiltrationSortFormSuccessFactory;
    }



    /**
     * @param CategoryEntity $categoryEntity
     * @return CategoryFiltrationSortForm
     */
    public function setCategoryEntity(CategoryEntity $categoryEntity) : self
    {
        $this->categoryEntity = $categoryEntity;
        return $this;
    }



    /**
     * @return CategoryEntity
     * @throws CategoryFiltrationSortFormException
     */
    public function getCategoryEntity() : CategoryEntity
    {
        if (!$this->categoryEntity instanceof CategoryEntity) {
            throw new CategoryFiltrationSortFormException(sprintf("You must set '%s' object.", CategoryEntity::class));
        }
        return $this->categoryEntity;
    }



    /**
     * @return SortForm
     */
    public function createComponentForm() : SortForm
    {
        $form = $this->sortFormFactory->create();
        $form->setItems($this->getFiltrationList());
        $form->setOnSuccess([$this, "formSuccess"]);
        return $form;
    }



    /**
     * @param Form $form
     * @param array $data
     */
    public function formSuccess(Form $form, array $data)
    {
        $process = $this->categoryFiltrationSortFormSuccessFactory->create();
        $process->process($this, $form, $data);
    }



    /**
     * @return array
     * @throws CategoryFiltrationSortFormException
     */
    protected function getFiltrationList() : array
    {
        //load all groups of filtration
        $repo = $this->categoryFiltrationRepositoryFactory->create();
        $filtration = $repo->findByCategoryId($this->categoryEntity->getId());

        if (!$filtration) {
            return [];
        }

        $data = [];
        foreach ($filtration as $f) {
            $group = $f->getProductParameterGroup();
            $translation = $group->getTranslation();
            $data[$f->getId()] = $translation->getName();
        }

        return $data;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . "/default.latte");
        $this->template->render();
    }
}
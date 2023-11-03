<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\Filtration\Combination\SortForm;

use App\Category\CategoryEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupSaveFacadeFactory;
use App\Components\SortForm\SortFormFactory;
use App\Helpers\Entities;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class SortForm extends Control
{


    /** @var Context */
    private $database;

    /** @var CategoryFiltrationGroupSaveFacadeFactory */
    private $groupFacadeFactory;

    /** @var CategoryFiltrationGroupRepository */
    private $groupRepo;

    /** @var SortFormFactory */
    private $sortFormFactory;

    /** @var CategoryEntity|null */
    private $category;



    public function __construct(CategoryFiltrationGroupRepository $categoryFiltrationGroupRepository,
                                CategoryFiltrationGroupSaveFacadeFactory $categoryFiltrationGroupSaveFacadeFactory,
                                Context $context,
                                SortFormFactory $sortFormFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->groupRepo = $categoryFiltrationGroupRepository;
        $this->groupFacadeFactory = $categoryFiltrationGroupSaveFacadeFactory;
        $this->sortFormFactory = $sortFormFactory;
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
     * @return \App\Components\SortForm\SortForm
     */
    public function createComponentForm() : \App\Components\SortForm\SortForm
    {
        $groups = $this->groupRepo->findByCategoryId((int)$this->category->getId());
        $groups = $groups ? Entities::toPair($groups, 'id', 'titleSeo') : [];

        $sortForm = $this->sortFormFactory->create();
        $sortForm->setItems($groups);
        $sortForm->setOnSuccess([$this, 'formSuccess']);
        return $sortForm;
    }



    /**
     * Handler for success sent form.
     * @param $form Form
     * @param $sorting array
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form, array $sorting)
    {
        $sorting = array_flip($sorting);
        $presenter = $this->getPresenter();

        $this->database->beginTransaction();
        $groupFacade = $this->groupFacadeFactory->create();
        $groupFacade->saveSort($sorting);
        $this->database->commit();

        $presenter->flashMessage('Řazení bylo uloženo.', 'success');
        $presenter->redirect('this');
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
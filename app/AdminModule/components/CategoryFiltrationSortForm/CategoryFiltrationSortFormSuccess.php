<?php

declare(strict_types = 1);

namespace App\Components\CategoryFiltrationSortForm;

use App\CategoryFiltration\CategoryFiltrationSortFacadeException;
use App\CategoryFiltration\CategoryFiltrationSortFacadeFactory;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationSortFormSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var CategoryFiltrationSortFacadeFactory */
    protected $categoryFiltrationSortFacadeFactory;



    public function __construct(Context $context,
                                CategoryFiltrationSortFacadeFactory $categoryFiltrationSortFacadeFactory)
    {
        $this->database = $context;
        $this->categoryFiltrationSortFacadeFactory = $categoryFiltrationSortFacadeFactory;
    }



    /**
     * @param CategoryFiltrationSortForm $sortForm
     * @param Form $form
     * @param array $data
     */
    public function process(CategoryFiltrationSortForm $sortForm, Form $form, array $data)
    {
        $presenter = $sortForm->getPresenter();

        try {
            $this->database->beginTransaction();
            $sortFacade = $this->categoryFiltrationSortFacadeFactory->create();
            $sortFacade->sort($sortForm->getCategoryEntity(), $data);
            $this->database->commit();

            $presenter->flashMessage("Řazení bylo uloženo.", "success");
            $presenter->redirect("this");
        } catch (CategoryFiltrationSortFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        }
    }
}
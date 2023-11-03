<?php

declare(strict_types = 1);

namespace App\Components\AdminModule\CategoryFiltrationForm;

use App\CategoryFiltration\CategoryFiltrationSaveFacadeException;
use App\CategoryFiltration\CategoryFiltrationSaveFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationFormSuccess extends Control
{


    /** @var Context */
    protected $database;

    /** @var CategoryFiltrationSaveFacadeFactory */
    protected $categoryFiltrationSaveFacadeFactory;



    /**
     * CategoryFiltrationFormSuccess constructor.
     * @param Context $database
     * @param CategoryFiltrationSaveFacadeFactory $categoryFiltrationSaveFacadeFactory
     */
    public function __construct(Context $database, CategoryFiltrationSaveFacadeFactory $categoryFiltrationSaveFacadeFactory)
    {
        $this->database = $database;
        $this->categoryFiltrationSaveFacadeFactory = $categoryFiltrationSaveFacadeFactory;
    }



    /**
     * @param Form $form
     * @param CategoryFiltrationForm $categoryForm
     */
    public function process(Form $form, CategoryFiltrationForm $categoryForm)
    {
        $presenter = $categoryForm->getPresenter();

        try {
            $values = $form->getValues();

            $this->database->beginTransaction();
            $facade = $this->categoryFiltrationSaveFacadeFactory->create();
            if ($filtration = $categoryForm->getCategoryFiltrationEntity()) {
                $facade->update($filtration);
            } else {
                $filtration = $facade->add($categoryForm->getCategoryEntity(), $values->groupId);
            }
            $this->database->commit();

            $presenter->flashMessage("Skupina byla přidána.", "success");

            //save and add a new
            if ($form->isSubmitted()->getName() == CategoryFiltrationForm::SUBMIT_ADD_NEW) {
                $categoryId = $categoryForm->getCategoryEntity()->getId();
                $presenter->redirect("CategoryFiltration:add", ["categoryId" => $categoryId]);
            }

            $presenter->redirect('this');
        } catch (CategoryFiltrationSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), "danger");
        }
    }
}
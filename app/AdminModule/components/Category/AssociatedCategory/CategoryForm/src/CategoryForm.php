<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\AssociatedCategory\CategoryForm;

use App\Category\AssociatedCategory\CategoryFacadeException;
use App\Category\AssociatedCategory\CategoryFacadeFactory AS AssociatedCategoryFacadeFactory;
use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryForm extends Control
{


    /** @var CategoryEntity|null */
    private $category;

    /** @var AssociatedCategoryFacadeFactory */
    private $categoryFacadeFactory;

    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var Context */
    private $database;



    public function __construct(AssociatedCategoryFacadeFactory $categoryFacadeFactory,
                                CategoryRepository $categoryRepository,
                                Context $context)
    {
        parent::__construct();
        $this->categoryFacadeFactory = $categoryFacadeFactory;
        $this->categoryRepo = $categoryRepository;
        $this->database = $context;
    }



    /**
     * @param $category CategoryEntity
     * @return void
     */
    public function setCategory(CategoryEntity $category)
    {
        $this->category = $category;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $form->addSelect('associatedCategoryId', 'Kategorie*', $this->getCategoryList())
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte kategorii.')
            ->setPrompt('- vyberte -');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $presenter = $this->getPresenter();
        $values = $form->getValues();

        try {
            $this->database->beginTransaction();
            $categoryFacade = $this->categoryFacadeFactory->create();
            $categoryFacade->save(NULL, $this->category->getId(), $values->associatedCategoryId);
            $this->database->commit();

            $presenter->flashMessage('Kategorie byla uložena.', 'success');
            $presenter->redirect('this');
        } catch (CategoryFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @return array
     */
    private function getCategoryList() : array
    {
        return $this->categoryRepo->findList();
    }
}
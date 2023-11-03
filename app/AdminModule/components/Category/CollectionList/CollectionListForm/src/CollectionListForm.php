<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\CollectionListForm;

use App\Category\CategoryEntity;
use App\Category\CategorySaveFacadeException;
use App\Category\CategorySaveFacadeFactory;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CollectionListForm extends Control
{


    /** @var CategoryEntity|null */
    private $category;

    /** @var CategorySaveFacadeFactory */
    private $categorySaveFacadeFactory;

    /** @var Context */
    private $database;



    public function __construct(CategorySaveFacadeFactory $categorySaveFacadeFactory,
                                Context $context)
    {
        parent::__construct();
        $this->categorySaveFacadeFactory = $categorySaveFacadeFactory;
        $this->database = $context;
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
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();
        $form->addCheckbox('show', ' Zobrazit kategorii v seznamu kolekcí na hlavní stránce')
            ->setDefaultValue($this->category->getCategorySlider());
        $form->addSelect('productSorter', 'Algoritmus pro řazení produktů', CategoryEntity::getProductSorterList())
			->setPrompt('- vyberte -')
			->setDefaultValue($this->category->getProductSorter())
			->setAttribute('class', 'form-control');
        $form->addSubmit('submit', 'Uložit')
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
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $categoryFacade = $this->categorySaveFacadeFactory->create();
            $categoryFacade->setAdvancedSettings($this->category->getId(), $values->show, $values->productSorter);
            $this->database->commit();

            $presenter->flashMessage('Nastavení bylo uloženo.', 'success');
            $presenter->redirect('this');
        } catch (CategorySaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\CategoryParameterForm;

use App\AdminModule\Components\ProductParameterSetForm\ProductParameterSetForm;
use App\Category\CategoryEntity;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeException;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeFactory;
use App\Product\Parameter\ParameterStorageFacadeFactory;
use App\ProductParameter\ProductParameterTranslationRepository;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class CategoryParameterForm extends ProductParameterSetForm
{


    /** @var CategoryProductParameterSaveFacadeFactory */
    private $categoryProductParameterSaveFacadeFactory;

    /** @var CategoryEntity|null */
    private $category;



    public function __construct(CategoryProductParameterSaveFacadeFactory $categoryProductParameterSaveFacadeFactory,
                                Context $database,
                                ProductParameterGroupTranslationRepository $groupParameterTranslationRepo,
                                ParameterStorageFacadeFactory $parameterStorageFacadeFactory,
                                ProductParameterTranslationRepository $parameterTranslationRepo)
    {
        parent::__construct($database, $groupParameterTranslationRepo, $parameterStorageFacadeFactory, $parameterTranslationRepo);
        $this->categoryProductParameterSaveFacadeFactory = $categoryProductParameterSaveFacadeFactory;
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
     * @return CategoryEntity
     * @throws \InvalidArgumentException missing category
     */
    public function getCategory() : CategoryEntity
    {
        if (!$this->category instanceof CategoryEntity) {
            throw new \InvalidArgumentException('Missing category.');
        }
        return $this->category;
    }



    /**
     * Handler for sent success form
     * @param $form Form
     * @return void
     * @throws \InvalidArgumentException
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $facade = $this->categoryProductParameterSaveFacadeFactory->create();
            $facade->add($this->getCategory()->getId(), $values->parameter);
            $this->database->commit();

            $presenter->flashMessage('Parametr byl uložen.', 'success');
            $presenter->redirect('this');
        } catch (CategoryProductParameterSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }
}
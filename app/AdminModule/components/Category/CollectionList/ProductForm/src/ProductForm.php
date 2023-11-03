<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\CollectionList\ProductForm;

use App\Category\CategoryEntity;
use App\Category\Product\Related\Product;
use App\Category\Product\Related\ProductFacadeException;
use App\Category\Product\Related\ProductFacadeFactory;
use App\Language\LanguageRepository;
use App\NotFoundException;
use App\Product\ProductListTrait;
use App\Product\ProductRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\Localization;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductForm extends Control
{


    use ProductListTrait;

    /** @var CategoryEntity|null */
    private $category;

    /** @var Context */
    private $database;

    /** @var LanguageRepository */
    private $languageRepo;

    /** @var ProductFacadeFactory */
    private $productFacadeFactory;

    /** @var ProductRepository */
    private $productRepo;



    public function __construct(Context $context,
                                LanguageRepository $languageRepository,
                                ProductFacadeFactory $productFacadeFactory,
                                ProductRepository $productRepository)
    {
        parent::__construct();
        $this->database = $context;
        $this->languageRepo = $languageRepository;
        $this->productFacadeFactory = $productFacadeFactory;
        $this->productRepo = $productRepository;
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
        $productList = $this->getProductList($this->productRepo, $this->getLocalization());

        $form = new Form();
        $form->addSelect('product', 'Produkt*', $productList)
            ->setPrompt('- Vyberte -')
            ->setAttribute('class', 'form-control select2')
            ->setRequired('Vyberte produkt.');
        $form->addSelect('type', 'Typ*', Product::getTypeList())
			->setPrompt('- Vyberte -')
			->setAttribute('class', 'form-control')
			->setRequired('Vyberte typ.');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $productFacade = $this->productFacadeFactory->create();
            $productFacade->add($this->category->getId(), (int)$values->product, $values->type);
            $this->database->commit();

            $presenter->flashMessage('Produkt byl přidán.', 'success');
            $presenter->redirect('this');
        } catch (ProductFacadeException $exception) {
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
     * @return Localization
     * @throws NotFoundException
     */
    private function getLocalization() : Localization
    {
        $language = $this->languageRepo->getOneById($this->category->getLanguageId());
        return new Localization($language->getId(), $language->getPrefix());
    }
}
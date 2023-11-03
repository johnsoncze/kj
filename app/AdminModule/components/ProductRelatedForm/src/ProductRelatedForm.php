<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductRelatedForm;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Product\ProductListTrait;
use App\Product\ProductRepository;
use App\Product\Related\Related;
use App\Product\Related\RelatedFacadeException;
use App\Product\Related\RelatedFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductRelatedForm extends Control
{


    use ProductListTrait;
    use ProductTrait;

    /** @var Context */
    private $database;

    /** @var LocalizationResolver */
    private $localizationResolver;

    /** @var ProductRepository */
    private $productRepo;

    /** @var RelatedFacadeFactory */
    private $relatedFacadeFactory;



    public function __construct(Context $database,
                                ProductRepository $productRepo,
                                RelatedFacadeFactory $relatedFacadeFactory)
    {
        parent::__construct();
        $this->database = $database;
        $this->localizationResolver = new LocalizationResolver();
        $this->productRepo = $productRepo;
        $this->relatedFacadeFactory = $relatedFacadeFactory;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $productId = $this->getProduct()->getId();
        $defaultLocalization = $this->localizationResolver->getDefault();
        $productList = $this->getProductList($this->productRepo, $defaultLocalization, [$productId]);

        $form = new Form();
        $form->addSelect('product', 'Produkt*', $productList)
            ->setPrompt('- Vyberte -')
            ->setAttribute('class', 'form-control select2')
            ->setRequired('Vyberte produkt.');
        $form->addSelect('type', 'Typ*', Related::getTypes(TRUE))
            ->setPrompt('- Vyberte -')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyberte typ.');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * Handler for successfully sent form.
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $facade = $this->relatedFacadeFactory->create();
            $facade->add((int)$this->getProduct()->getId(), $values->product, $values->type);
            $this->database->commit();

            $presenter->flashMessage('Produkt byl přidán.', 'success');
            $this->redirect('this');
        } catch (RelatedFacadeException $exception) {
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
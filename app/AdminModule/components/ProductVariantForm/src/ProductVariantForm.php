<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductVariantForm;

use App\Helpers\Entities;
use App\Product\Parameter\ProductParameterRepository;
use App\Product\Product;
use App\Product\ProductListTrait;
use App\Product\ProductRepository;
use App\Product\Variant\VariantRepository;
use App\Product\Variant\VariantStorageFacadeException;
use App\Product\Variant\VariantStorageFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupTranslationRepository;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductVariantForm extends Control
{


    use ProductListTrait;
    use ProductTrait;

    /** @var Context */
    private $database;

    /** @var LocalizationResolver */
    protected $localizationResolver;

    protected $productRepo;

    /** @var ProductParameterGroupTranslationRepository */
    private $productParameterGroupTranslationRepo;

    /** @var ProductParameterRepository */
    private $productParameterRelationRepo;

    /** @var VariantRepository */
    private $variantRepo;

    /** @var VariantStorageFacadeFactory */
    private $variantStorageFacadeFactory;



    public function __construct(Context $database,
                                ProductParameterGroupTranslationRepository $productParameterGroupTranslationRepository,
                                ProductParameterRepository $productParameterRepository,
                                ProductRepository $productRepository,
                                VariantRepository $variantRepo,
                                VariantStorageFacadeFactory $variantStorageFacadeFactory)
    {
        parent::__construct();
        $this->database = $database;
        $this->localizationResolver = new LocalizationResolver();
        $this->productRepo = $productRepository;
        $this->productParameterGroupTranslationRepo = $productParameterGroupTranslationRepository;
        $this->productParameterRelationRepo = $productParameterRepository;
        $this->variantRepo = $variantRepo;
        $this->variantStorageFacadeFactory = $variantStorageFacadeFactory;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $defaultLocalization = $this->localizationResolver->getDefault();
        $groupList = $this->getGroupList($this->product, $defaultLocalization);
        $productList = $this->getProductList($this->productRepo, $defaultLocalization, [$this->product->getId()]);
        $parentVariantList = $this->getParentVariantList();

        $form = new Form();
        $form->addSelect('group', 'Skupina parametrů*', $groupList)
            ->setAttribute('class', 'form-control select2')
            ->setPrompt('- Vyberte -')
            ->setRequired('Vyberte skupinu.');
        $form->addSelect('productVariant', 'Produkt*', $productList)
            ->setAttribute('class', 'form-control select2')
            ->setPrompt('- Vyberte -')
            ->setRequired('Vyberte produktu.');
        $form->addSelect('parentVariantId', 'Nadřazená varianta', $parentVariantList)
            ->setAttribute('class', 'form-control select2')
            ->setPrompt('- Vyberte -');
        $form->addSubmit('submit', 'Přidat')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];
        return $form;
    }



    /**
     * Form success handle.
     * @param $form Form
     */
    public function formSuccess(Form $form)
    {
        $product = $this->getProduct();
        $values = $form->getValues();
        $presenter = $this->getPresenter();

        try {
            $this->database->beginTransaction();
            $variantFacade = $this->variantStorageFacadeFactory->create();
            $variantFacade->add($product->getId(), $values->productVariant, $values->group, $values->parentVariantId);
            $this->database->commit();
            $presenter->flashMessage('Varianta byla uložena.', 'success');
            $presenter->redirect('this');
        } catch (VariantStorageFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $product Product
     * @param $localization Localization
     * @return array
     */
    private function getGroupList(Product $product, Localization $localization) : array
    {
        $groups = [];
        $parameters = $this->productParameterRelationRepo->findByProductId($product->getId());
        if ($parameters) {
            $groups = $this->productParameterGroupTranslationRepo->findByMoreParameterIdAndLanguageId(Entities::getProperty($parameters, 'parameterId'), $localization->getId());
        }
        return $groups ? Entities::toPair($groups, 'productParameterGroupId', 'name') : [];
    }



    /**
     * @return array
     * todo replace column names by annotation from entity
     */
    private function getParentVariantList() : array
    {
        $variantList = [];
        $variants = $this->variantRepo->findJoinedByProductId($this->product->getId());
        foreach ($variants as $variant) {
            $value = sprintf('%s: %s - %s', $variant['p_code'], $variant['ppgt_name'], $variant['ppt_value']);
            $variantList[$variant['pv_id']] = $value;
        }
        return $variantList;
    }
}
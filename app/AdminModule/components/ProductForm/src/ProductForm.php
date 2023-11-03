<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductForm;

use App\Components\OgFormContainer\OgFormContainer;
use App\Components\OgFormContainer\OgFormContainerFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\TranslationFormTrait;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Arrays;
use App\Helpers\Entities;
use App\Helpers\Images;
use App\Helpers\Regex;
use App\Helpers\Summernote;
use App\Product\AdditionalPhoto\ProductAdditionalPhoto;
use App\Product\Parameter\ProductParameter;
use App\Product\Product;
use App\Product\Translation\ProductTranslation;
use App\ProductState\Translation\ProductStateTranslationRepositoryFactory;
use Nette\Application\UI\Form;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductForm extends AbstractProductForm
{


    use TranslationFormTrait;

    /** @var ProductStateTranslationRepositoryFactory */
    protected $stateTranslationRepositoryFactory;

    /** @var UrlFormContainerFactory */
    protected $urlFormFactory;

    /** @var SeoFormContainerFactory */
    protected $seoFormFactory;

    /** @var ProductFormSuccessFactory */
    protected $productFormSuccessFactory;

    /******** Entities ********/

    /** @var array|ProductParameter[] */
    protected $productParameters = [];

    /** @var array|ProductAdditionalPhoto[] */
    protected $productAdditionalPhotos = [];

    /** @var OgFormContainerFactory */
    protected $ogFormContainerFactory;


    public function __construct(
        ProductStateTranslationRepositoryFactory $stateTranslationRepositoryFactory,
        UrlFormContainerFactory $urlFormFactory,
        SeoFormContainerFactory $seoFormFactory,
        ProductFormRemovePhotoFactory $productFormRemovePhotoFactory,
        ProductFormSuccessFactory $productFormSuccessFactory,
        OgFormContainerFactory $ogFormContainerFactory
    ) {
        $this->ogFormContainerFactory = $ogFormContainerFactory;
        parent::__construct($productFormRemovePhotoFactory);
        $this->stateTranslationRepositoryFactory = $stateTranslationRepositoryFactory;
        $this->urlFormFactory = $urlFormFactory;
        $this->seoFormFactory = $seoFormFactory;
        $this->productFormSuccessFactory = $productFormSuccessFactory;
    }



    /**
     * @param ProductAdditionalPhoto $productAdditionalPhoto
     * @return ProductForm
     */
    public function addProductAdditionalPhoto(ProductAdditionalPhoto $productAdditionalPhoto) : self
    {
        if (!in_array($productAdditionalPhoto, $this->productAdditionalPhotos, TRUE)) {
            $this->productAdditionalPhotos[] = $productAdditionalPhoto;
        }
        return $this;
    }



    /**
     * @return array|ProductAdditionalPhoto[]
     */
    public function getProductAdditionalPhotos() : array
    {
        return $this->productAdditionalPhotos;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $stateList = $this->getProductStateList();

        $form = new Form();

        //basic data
        $form->addText('name', 'Název*')
            ->setAttribute('class', 'form-control')
            ->setAttribute('autofocus')
            ->setRequired('Vyplňte název.');
        $externalSystemId = $form->addText('externalSystemId', 'Id v externím systému')
            ->setAttribute('class', 'form-control')
			->setRequired(FALSE);
        $externalSystemId->addRule(Form::INTEGER, 'Id musí být číslo');
        $externalSystemId->addRule(Form::MIN, 'Id musí být \'1\' nebo větší.', 1);
        $externalSystemId->addRule(Form::MAX, 'Id může být maximálně \'8388607\'.', Product::MAX_EXTERNAL_SYSTEM_ID);
        $form->addText('code', 'Kód zboží*')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte kód.');

        //states
        $form->addCheckbox('completed', ' Dokončeno')
            ->setDefaultValue($this->presenter->getParameter('completed') !== 'false');
        $form->addTextArea('commentCompleted', 'Poznámka k dokončení')
            ->setAttribute('class', 'form-control');
        $form->addSelect('state', 'Stav', Arrays::toPair(Product::getStates(), 'key', 'translation'))
            ->setRequired('Zvolte stav.')
            ->setAttribute('class', 'form-control');
        $form->addSelect('stockState', 'Stav při skladové dostupnosti*', $stateList)
			->setPrompt('- vyberte - ')
            ->setRequired('Vyberte stav.')
            ->setAttribute('class', 'form-control');
        $form->addSelect('emptyStockState', 'Stav při skladové nedostupnosti*', $stateList)
			->setPrompt('- vyberte - ')
            ->setRequired('Vyberte stav.')
            ->setAttribute('class', 'form-control');

        //stock
        $form->addText('stock', 'Skladové množství*')
            ->setRequired('Vyplňte skladové množství.')
            ->addRule(Form::NUMERIC, 'Hodnota musí být číslo.')
            ->setAttribute('class', 'form-control')
            ->setDefaultValue(0);

        //additional data
        $form->addText('newUntilTo', 'Zobrazit jako novinku do:')
			->setHtmlId('newUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
        $form->addText('limitedUntilTo', 'Zobrazit jako limitovanou edici do:')
			->setHtmlId('limitedUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
        $form->addText('bestsellerUntilTo', 'Zobrazit jako nejprodávanější do:')
			->setHtmlId('bestsellerUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
        $form->addText('goodpriceUntilTo', 'Zobrazit jako výhodnou cenu do:')
			->setHtmlId('goodpriceUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
        $form->addText('rareUntilTo', 'Zobrazit jako výjimečný šperk do:')
			->setHtmlId('rareUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
				
				
        $form->addCheckbox('saleOnline', ' Lze zakoupit online')->setDefaultValue(TRUE);
        $form->addTextArea('description', 'Popis')
            ->setAttribute('class', 'form-control')
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE)
			->setHtmlId('ckEditor');
        $form->addTextArea('shortDescription', 'Krátký popis')
			->setAttribute('class', 'form-control');

        //photos
        $form->addUpload('photo', 'Úvodní fotografie')
            ->setRequired(FALSE)
            ->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
        $form->addMultiUpload('additionalPhotos', 'Doplňkové fotografie')
            ->setRequired(FALSE)
            ->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());

        //price
        $form->addText('price', 'Cena*')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte cenu.')
            ->addRule(Form::FLOAT, 'Cena musí být ve formátu např. 100.50');
        $form->addSelect('vat', 'DPH', Product::getVatList())
            ->setAttribute('class', 'form-control');
        $form->addSelect('discountAllowed', 'Povolené slevy', [
        	1 => 'Ano',
			0 => 'Ne',
		])->setDefaultValue(TRUE)
			->setAttribute('class', 'form-control');

        //add form components
        $form->addComponent($this->urlFormFactory->create(), UrlFormContainer::NAME);
        $form->addComponent($this->seoFormFactory->create(), SeoFormContainer::NAME);
        $form->addComponent($this->ogFormContainerFactory->create(), OgFormContainer::NAME);

        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');

        //set default values
        $this->setDefaultFormValues($form);

        //success action
        $form->onSuccess[] = function (Form $form) {
            $formSuccess = $this->productFormSuccessFactory->create();
            $formSuccess->execute($form, $this);
        };

        return $form;
    }



    public function render()
    {
        parent::render();
        $this->template->additionalPhotos = $this->getProductAdditionalPhotos();

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param Form $form
     * @return Form
     */
    protected function setDefaultFormValues(Form $form) : Form
    {
        if ($product = $this->getProduct()) {
            /** @var $translation ProductTranslation */
			$locale = $this->getLocale();
            $translation = $product->getTranslation($locale->getPrefix());

            $values = $product->toArray();
            $values['newUntilTo'] = $product->getNewUntilTo() ? (new \DateTime($product->getNewUntilTo()))->format('d.m.Y') : NULL;
            $values['limitedUntilTo'] = $product->getLimitedUntilTo() ? (new \DateTime($product->getLimitedUntilTo()))->format('d.m.Y') : NULL;
            $values['bestsellerUntilTo'] = $product->getBestsellerUntilTo() ? (new \DateTime($product->getBestsellerUntilTo()))->format('d.m.Y') : NULL;
            $values['goodpriceUntilTo'] = $product->getGoodpriceUntilTo() ? (new \DateTime($product->getGoodpriceUntilTo()))->format('d.m.Y') : NULL;
            $values['rareUntilTo'] = $product->getRareUntilTo() ? (new \DateTime($product->getRareUntilTo()))->format('d.m.Y') : NULL;
            $values['name'] = $translation->getName();
            $values['description'] = $translation->getDescription();
            $values['shortDescription'] = $translation->getShortDescription();
            $values[UrlFormContainer::NAME]['url'] = $translation->getUrl();
            $values[SeoFormContainer::NAME]['titleSeo'] = $translation->getTitleSeo();
            $values[SeoFormContainer::NAME]['descriptionSeo'] = $translation->getDescriptionSeo();
            $values[OgFormContainer::NAME]['titleOg'] = $translation->getTitleOg();
            $values[OgFormContainer::NAME]['descriptionOg'] = $translation->getDescriptionOg();

            $form->setDefaults($values);
        }
        return $form;
    }



    /**
     * @return array
     */
    protected function getProductStateList() : array
    {
        $locale = new LocalizationResolver();
        $lang = $locale->getDefault();

        $stateTranslationRepo = $this->stateTranslationRepositoryFactory->create();
        $states = $stateTranslationRepo->findByLanguageId($lang->getId());

        return $states ? Entities::toPair($states, 'stateId', 'value') : [];
    }
}
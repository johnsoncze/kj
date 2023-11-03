<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\WeedingRingForm;

use App\AdminModule\Components\ProductForm\AbstractProductForm;
use App\AdminModule\Components\ProductForm\ProductFormRemovePhotoFactory;
use App\Components\OgFormContainer\OgFormContainer;
use App\Components\OgFormContainer\OgFormContainerFactory;
use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\SeoFormContainer\SeoFormContainerFactory;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Components\UrlFormContainer\UrlFormContainerFactory;
use App\Helpers\Arrays;
use App\Helpers\Images;
use App\Helpers\Regex;
use App\Helpers\Summernote;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoRepository;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\Product;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Product\Ring\Size\Size;
use App\Product\Ring\Size\SizeRepository AS RingSizeRepository;
use App\Product\Translation\ProductTranslation;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use App\Product\WeedingRing\Gender\Gender;
use App\Product\WeedingRing\Size\SizeFacadeException;
use App\Product\WeedingRing\Size\SizeFacadeFactory;
use App\Product\WeedingRing\Size\SizeRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class WeedingRingForm extends AbstractProductForm
{
    /** @var OgFormContainerFactory */
    protected $ogFormContainerFactory;

    /** @var ProductAdditionalPhotoSaveFacadeFactory */
    private $additionalPhotoSaveFacadeFactory;

    /** @var Context */
    private $database;

    /** @var ProductAdditionalPhotoRepository */
    private $productAdditionalPhotoRepo;

    /** @var ProductSaveFacadeFactory */
    private $productSaveFacadeFactory;

    /** @var ProductTranslationSaveFacadeFactory */
    private $productSaveTranslationFacadeFactory;

    /** @var Size[]|array */
    private $ringSizes;

    /** @var UrlFormContainerFactory */
    private $urlFormFactory;

    /** @var SeoFormContainerFactory */
    private $seoFormFactory;

    /** @var SizeRepository */
    private $sizeRepo;

    /** @var SizeFacadeFactory */
    private $weedingRingSizeFacadeFactory;


    public function __construct(
        Context $context,
        ProductAdditionalPhotoRepository $productAdditionalPhotoRepository,
        ProductAdditionalPhotoSaveFacadeFactory $productAdditionalPhotoSaveFacadeFactory,
        ProductFormRemovePhotoFactory $productFormRemovePhotoFactory,
        ProductSaveFacadeFactory $productSaveFacadeFactory,
        ProductTranslationSaveFacadeFactory $productTranslationSaveFacadeFactory,
        RingSizeRepository $ringSizeRepository,
        SeoFormContainerFactory $seoFormContainerFactory,
        SizeFacadeFactory $sizeFacadeFactory,
        SizeRepository $sizeRepository,
        UrlFormContainerFactory $urlFormContainerFactory,
        OgFormContainerFactory $ogFormContainerFactory
    ) {
        $this->ogFormContainerFactory = $ogFormContainerFactory;
        parent::__construct($productFormRemovePhotoFactory);
        $this->additionalPhotoSaveFacadeFactory = $productAdditionalPhotoSaveFacadeFactory;
        $this->database = $context;
        $this->productAdditionalPhotoRepo = $productAdditionalPhotoRepository;
        $this->productSaveFacadeFactory = $productSaveFacadeFactory;
        $this->productSaveTranslationFacadeFactory = $productTranslationSaveFacadeFactory;
        $this->seoFormFactory = $seoFormContainerFactory;
        $this->sizeRepo = $sizeRepository;
        $this->urlFormFactory = $urlFormContainerFactory;
        $this->weedingRingSizeFacadeFactory = $sizeFacadeFactory;

        $this->ringSizes = $ringSizeRepository->findAll();
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();

        //basic data
        $form->addText('name', 'Název*')
            ->setAttribute('class', 'form-control')
            ->setAttribute('autofocus')
            ->setRequired('Vyplňte název.');
        $form->addText('code', 'Kód zboží*')
            ->setAttribute('class', 'form-control')
            ->setRequired('Vyplňte kód.');

        //state
        $form->addCheckbox('completed', ' Dokončeno')
            ->setDefaultValue($this->presenter->getParameter('completed') !== 'false');
        $form->addTextArea('commentCompleted', 'Poznámka k dokončení')
            ->setAttribute('class', 'form-control');
        $form->addSelect('state', 'Stav', Arrays::toPair(Product::getStates(), 'key', 'translation'))
            ->setRequired('Zvolte stav.')
            ->setAttribute('class', 'form-control');

        //additional data
		$form->addText('newUntilTo', 'Zobrazit jako novinku do:')
			->setHtmlId('newUntilTo')
			->setAttribute('class', 'form-control datepicker')
			->setAttribute('autocomplete', 'off')
			->addCondition(Form::FILLED)
			->addRule(Form::PATTERN, 'Datum musí být ve formátu např. 31.12.2018', Regex::DATE);
        $form->addTextArea('description', 'Popis')
            ->setAttribute('class', 'form-control')
			->setHtmlId('ckEditor')
            ->setEmptyValue(Summernote::EMPTY_STRING_VALUE);

        //photos
        $form->addUpload('photo', 'Úvodní fotografie')
            ->setRequired(FALSE)
            ->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());
        $form->addMultiUpload('additionalPhotos', 'Doplňkové fotografie')
            ->setRequired(FALSE)
            ->addRule(Form::MIME_TYPE, 'Nahrávejte pouze obrázek.', Images::getMimeTypes());

        //add form components
        $form->addComponent($this->urlFormFactory->create(), UrlFormContainer::NAME);
        $form->addComponent($this->seoFormFactory->create(), SeoFormContainer::NAME);
        $form->addComponent($this->ogFormContainerFactory->create(), OgFormContainer::NAME);

        //sizes
        $maleSizeContainer = $form->addContainer(Gender::MALE);
        $femaleSizeContainer = $form->addContainer(Gender::FEMALE);
        foreach ($this->ringSizes as $sizeObject) {
            $size = $sizeObject->getSize();
            $maleSizeContainer->addText($sizeObject->getId(), $size)
                ->setAttribute('class', 'form-control')
                ->setAttribute('placeholder', 'Vložte cenu..')
                ->setRequired('Vyplňte cenu.')
                ->addCondition(Form::FLOAT, 'Cena musí být ve formátu např.: 123.50');
            $femaleSizeContainer->addText($sizeObject->getId(), $size)
                ->setAttribute('class', 'form-control')
                ->setAttribute('placeholder', 'Vložte cenu..')
                ->setRequired('Vyplňte cenu.')
                ->addCondition(Form::FLOAT, 'Cena musí být ve formátu např.: 123.50');
        }

        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $form->onSuccess[] = [$this, 'formSuccess'];

        //set default values
        $this->setDefaultFormValues($form);

        return $form;
    }



    public function render()
    {
        parent::render();
        $this->template->additionalPhotos = $this->product ? $this->productAdditionalPhotoRepo->findByProductId($this->product->getId()) : [];
        $this->template->ringSizes = $this->ringSizes;

        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }



    /**
     * @param $form Form
     * @return void
     * @throws AbortException
     */
    public function formSuccess(Form $form)
    {
        $languageId = $this->getLocale()->getId();
        $presenter = $this->getPresenter();
        $values = $form->getValues();

        $productId = $this->product ? $this->product->getId() : NULL;
        $externalSystemId = NULL;
        $stock = 0;
        $stockStateId = Product::WEEDING_RING_PAIR_TYPE_DEFAULT_STATE_ID;
        $completedDescription = $values->commentCompleted ?: NULL;
        $description = $values->description ?: NULL;
        $url = $values[UrlFormContainer::NAME]->url ?: NULL;
        $titleSeo = $values[SeoFormContainer::NAME]->titleSeo ?: NULL;
        $descriptionSeo = $values[SeoFormContainer::NAME]->descriptionSeo ?: NULL;
        $titleOg = $values[OgFormContainer::NAME]->titleOg ?: NULL;
        $descriptionOg = $values[OgFormContainer::NAME]->descriptionOg ?: NULL;
        $vat = Product::WEEDING_RING_PAIR_TYPE_VAT;
        $price = 1.0;
        $type = Product::WEEDING_RING_PAIR_TYPE;
        $newUntilTo = $values->newUntilTo ? new \DateTime($values->newUntilTo) : NULL;

        try {
            $this->database->beginTransaction();
            $productFacade = $this->productSaveFacadeFactory->create();

            //save product
            $product = $this->product
                ? $productFacade->update(
                    $productId,
                    $externalSystemId,
                    $values->code,
                    $stockStateId,
                    $stockStateId,
                    $stock,
                    $price,
                    $vat,
                    $values->state,
                    $newUntilTo,
                    NULl,
                    NULL,
                    NULL,
                    NULL,
                    TRUE,
                    $values->completed,
                    $completedDescription,
                    $type)
                : $productFacade->saveNew($values->code,
                    $externalSystemId,
                    $stockStateId,
                    $stockStateId,
                    $stock,
                    $price,
                    $vat,
                    $values->state,
                    $newUntilTo,
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    TRUE,
                    $values->completed,
                    $completedDescription,
                    $type);

            //save sizes
            $maleSizes = $values[Gender::MALE];
            $femaleSizes = $values[Gender::FEMALE];
            $sizeFacade = $this->weedingRingSizeFacadeFactory->create();
            foreach ($maleSizes as $size => $price) {
                $sizeFacade->save($product->getId(), (int)$size, Gender::MALE, $price);
            }
            foreach ($femaleSizes as $size => $price) {
                $sizeFacade->save($product->getId(), (int)$size, Gender::FEMALE, $price);
            }

            //set price
			$product = $productFacade->refreshWeedingRingPairPriceById($product->getId());

            //save translation
            $productTranslationFacade = $this->productSaveTranslationFacadeFactory->create();
            $productTranslation = $this->product
                ? $productTranslationFacade->update(
                    (int)$this->product->getTranslationById($languageId)->getId(), $values->name, $description, $url, $titleSeo, $descriptionSeo,null, $titleOg, $descriptionOg)
                : $productTranslationFacade->saveNew($product->getId(), $languageId, $values->name, $description, $url, $titleSeo, $descriptionSeo,null, $titleOg, $descriptionOg);

            //save main photo
            if ($values->photo->isOk()) {
                $productFacade->savePhoto($product, $values->photo);
            }

            //save additional photos
            $additionalPhotos = $values->additionalPhotos;
            if ($additionalPhotos) {
                $saveFacade = $this->additionalPhotoSaveFacadeFactory->create();
                $saveFacade->add($product, $additionalPhotos);
            }

            $this->database->commit();
            $presenter->flashMessage('Produkt byl uložen.', 'success');
            $presenter->redirect('Product:edit', ['id' => $product->getId()]);
        } catch (ProductSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (ProductTranslationSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (SizeFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (ProductAdditionalPhotoSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        }
    }



    /**
     * @param $form Form
     * @return Form
     */
    private function setDefaultFormValues(Form $form) : Form
    {
        if ($product = $this->getProduct()) {
            $locale = $this->getLocale();
            /** @var ProductTranslation $translation */
            $translation = $product->getTranslation($locale->getPrefix());
            $values = $product->toArray();
            $values['name'] = $translation->getName();
            $values['newUntilTo'] = $product->getNewUntilTo() ? (new \DateTime($product->getNewUntilTo()))->format('d.m.Y') : NULL;
            $values['description'] = $translation->getDescription();
            $values[UrlFormContainer::NAME]['url'] = $translation->getUrl();
            $values[SeoFormContainer::NAME]['titleSeo'] = $translation->getTitleSeo();
            $values[SeoFormContainer::NAME]['descriptionSeo'] = $translation->getDescriptionSeo();
            $values[OgFormContainer::NAME]['titleOg'] = $translation->getTitleOg();
            $values[OgFormContainer::NAME]['descriptionOg'] = $translation->getDescriptionOg();
            //size
            $sizes = $this->sizeRepo->findByProductId($product->getId());
            foreach ($sizes as $size) {
                $values[$size->getGender()][$size->getSizeId()] = $size->getPrice();
            }

            $form->setDefaults($values);
        }
        return $form;
    }

}
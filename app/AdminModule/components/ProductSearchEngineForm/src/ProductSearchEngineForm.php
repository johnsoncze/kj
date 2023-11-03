<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductSearchEngineForm;

use App\AdminModule\Components\ProductVariantForm\ProductTrait;
use App\Components\TranslationFormTrait;
use App\FrontModule\Components\Product\GoogleMerchantFeed\GoogleMerchantFeed;
use App\Product\ProductSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Database\Context;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductSearchEngineForm extends Control
{


    use ProductTrait;
    use TranslationFormTrait;

    /** @var Context */
    private $database;

    /** @var ProductTranslationSaveFacadeFactory */
    private $productTranslationSaveFacadeFactory;

    /** @var IStorage */
    private $storage;



    public function __construct(Context $context,
                                IStorage $storage,
                                ProductTranslationSaveFacadeFactory $productTranslationSaveFacadeFactory)
    {
        parent::__construct();
        $this->database = $context;
        $this->productTranslationSaveFacadeFactory = $productTranslationSaveFacadeFactory;
        $this->storage = $storage;
    }



    /**
     * @return Form
     */
    public function createComponentForm() : Form
    {
        $form = new Form();

        //google merchant feed
        $form->addText('googleMerchantCategory', 'Kategorie (generováno automaticky)')
            ->setAttribute('class', 'form-control')
            ->setDisabled(TRUE);
        $form->addText('googleMerchantBrand', 'Brand (generováno automaticky)')
            ->setAttribute('class', 'form-control')
            ->setDisabled(TRUE);
        $form->addText('googleMerchantTitle', 'Titulek')
            ->setAttribute('class', 'form-control')
            ->setMaxLength(255);

        //zbozi.cz
        $form->addText('zboziCzCategory', 'Kategorie (generováno automaticky)')
            ->setAttribute('class', 'form-control')
            ->setDisabled(TRUE);

        //heureka
        $form->addText('heurekaCategory', 'Kategorie (generováno automaticky)')
            ->setAttribute('class', 'form-control')
            ->setDisabled(TRUE);

        $form->addSubmit('submit', 'Uložit')
            ->setAttribute('class', 'btn btn-success');
        $this->setDefaultFormValues($form);
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }



    /**
     * Handler for success sent form.
     * @param $form Form
     * @return void
     */
    public function formSuccess(Form $form)
    {
        $values = $form->getValues();
        $product = $this->getProduct();
        $locale = $this->getLocale();
        $translation = $product->getTranslation($locale->getPrefix());
        $presenter = $this->getPresenter();
        $googleMerchantTitle = $values->googleMerchantTitle ?: NULL;

        try {
            $translationSaveFacade = $this->productTranslationSaveFacadeFactory->create();

            $this->database->beginTransaction();
            $translationSaveFacade->saveProductSearchEngines((int)$translation->getId(), $googleMerchantTitle);
            $this->database->commit();

            //delete product feeds
            if ($googleMerchantTitle !== NULL) {
                //todo send request to queue for generate new feeds
                $cache = new Cache($this->storage, 'Nette.Templating.Cache');
                $cache->clean([
                    Cache::TAGS => [GoogleMerchantFeed::CACHE_TAG],
                ]);
            }

            $presenter->flashMessage('Formulář byl uložen.', 'success');
            $presenter->redirect('this');
        } catch (ProductSaveFacadeException $exception) {
            $this->database->rollBack();
            $presenter->flashMessage($exception->getMessage(), 'danger');
        } catch (ProductTranslationSaveFacadeException $exception) {
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
     * Set default form values.
     * @param $form Form
     * @return Form
     */
    private function setDefaultFormValues(Form $form) : Form
    {
        try {
            $product = $this->getProduct();
            $locale = $this->getLocale();
            $form->setDefaults([
                'googleMerchantCategory' => $product->getGoogleMerchantCategory(),
                'googleMerchantBrand' => $product->getGoogleMerchantBrandText(),
                'googleMerchantTitle' => $product->getTranslation($locale->getPrefix())->getGoogleMerchantTitle() ?: NULL,
                'heurekaCategory' => $product->getHeurekaCategory(),
                'zboziCzCategory' => $product->getZboziCzCategory(),
            ]);
        } catch (\InvalidArgumentException $exception) {
            //nothing..
        }

        return $form;
    }
}
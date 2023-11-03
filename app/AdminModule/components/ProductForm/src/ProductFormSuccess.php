<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductForm;

use App\Components\SeoFormContainer\SeoFormContainer;
use App\Components\UrlFormContainer\UrlFormContainer;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeException;
use App\Product\AdditionalPhoto\ProductAdditionalPhotoSaveFacadeFactory;
use App\Product\Product;
use App\Product\ProductSaveFacade;
use App\Product\ProductSaveFacadeException;
use App\Product\ProductSaveFacadeFactory;
use App\Product\Translation\ProductTranslationSaveFacadeException;
use App\Product\Translation\ProductTranslationSaveFacadeFactory;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use Nette\Http\FileUpload;
use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFormSuccess extends NObject
{


    /** @var Context */
    protected $database;

    /** @var ProductSaveFacadeFactory */
    protected $productSaveFacadeFactory;

    /** @var ProductTranslationSaveFacadeFactory */
    protected $productTranslationSaveFacadeFactory;

    /** @var ProductAdditionalPhotoSaveFacadeFactory */
    protected $productAdditionalPhotoSaveFacadeFactory;



    /**
     * ProductFormSuccess constructor.
     * @param Context $database
     * @param ProductSaveFacadeFactory $productSaveFacadeFactory
     * @param ProductTranslationSaveFacadeFactory $productTranslationSaveFacadeFactory
     * @param ProductAdditionalPhotoSaveFacadeFactory $productAdditionalPhotoSaveFacadeFactory
     */
    public function __construct(Context $database,
                                ProductSaveFacadeFactory $productSaveFacadeFactory,
                                ProductTranslationSaveFacadeFactory $productTranslationSaveFacadeFactory,
                                ProductAdditionalPhotoSaveFacadeFactory $productAdditionalPhotoSaveFacadeFactory)
    {
        $this->database = $database;
        $this->productSaveFacadeFactory = $productSaveFacadeFactory;
        $this->productTranslationSaveFacadeFactory = $productTranslationSaveFacadeFactory;
        $this->productAdditionalPhotoSaveFacadeFactory = $productAdditionalPhotoSaveFacadeFactory;
    }



    /**
     * @param Form $form
     * @param ProductForm $productForm
     */
    public function execute(Form $form, ProductForm $productForm)
    {
        $presenter = $productForm->getPresenter();
        $values = $form->getValues();
        $product = $productForm->getProduct();

        try {
            $this->database->beginTransaction();

            //save
            $product = $product instanceof Product
                ? $this->updateProduct($values, $product, $productForm)
                : $this->saveNewProduct($values, $productForm);
            $this->saveAdditionalPhotos($product, $values);

            $this->database->commit();

            $presenter->flashMessage('Produkt byl uložen.', 'success');
            $presenter->redirect('Product:edit', ['id' => $product->getId()]);

        } catch (ProductSaveFacadeException $exception) {
            $this->sendErrorMessage($presenter, $exception);
            $this->database->rollBack();
        } catch (ProductTranslationSaveFacadeException $exception) {
            $this->sendErrorMessage($presenter, $exception);
            $this->database->rollBack();
        } catch (ProductAdditionalPhotoSaveFacadeException $exception) {
            $this->sendErrorMessage($presenter, $exception);
            $this->database->rollBack();
        }
    }



    /**
     * @param $values ArrayHash
     * @param $productForm ProductForm
     * @return Product
     * @throws ProductSaveFacadeException
     * @throws ProductTranslationSaveFacadeException
     */
    protected function saveNewProduct(ArrayHash $values, ProductForm $productForm) : Product
    {
	    	$newUntilTo = $values->newUntilTo ? new \DateTime($values->newUntilTo) : NULL;
	    	$limitedUntilTo = $values->newUntilTo ? new \DateTime($values->limitedUntilTo) : NULL;
	    	$bestsellerUntilTo = $values->newUntilTo ? new \DateTime($values->bestsellerUntilTo) : NULL;
	    	$goodpriceUntilTo = $values->newUntilTo ? new \DateTime($values->goodpriceUntilTo) : NULL;
	    	$rareUntilTo = $values->newUntilTo ? new \DateTime($values->rareUntilTo) : NULL;
        $locale = $productForm->getLocale();
        $saveFacade = $this->productSaveFacadeFactory->create();
        $translationSaveFacade = $this->productTranslationSaveFacadeFactory->create();

        $product = $saveFacade->saveNew($values->code, $values->externalSystemId ? (int)$values->externalSystemId : NULL, $values->stockState, $values->emptyStockState,
																				$values->stock, $values->price, (float)$values->vat, $values->state,
																				$newUntilTo, $limitedUntilTo, $bestsellerUntilTo, $goodpriceUntilTo, $rareUntilTo, 
																				TRUE, $values->completed === TRUE, $values->commentCompleted ?: NULL,
			Product::DEFAULT_TYPE, (bool)$values->discountAllowed);

        $translation = $translationSaveFacade->saveNew($product->getId(), $locale->getId(),
            $values->name, $values->description, $this->getUrl($values), $this->getTitleSeo($values), $this->getDescriptionSeo($values),
            $values->shortDescription ?: null, $values->ogForm->titleOg ?? null, $values->ogForm->descriptionOg ?? null);

        $product->addTranslation($translation);
        $this->saveMainPhoto($product, $values->photo, $saveFacade);

        return $product;
    }



    /**
     * @param ArrayHash $values
     * @param Product $product
     * @param $productForm ProductForm
     * @return Product
     * @throws ProductSaveFacadeException
     * @throws ProductTranslationSaveFacadeException
     */
    protected function updateProduct(ArrayHash $values, Product $product, ProductForm $productForm) : Product
    {
        $locale = $productForm->getLocale();
        $translation = $product->getTranslation($locale->getPrefix());
        $saveFacade = $this->productSaveFacadeFactory->create();
        $translationSaveFacade = $this->productTranslationSaveFacadeFactory->create();
				$newUntilTo = $values->newUntilTo ? new \DateTime($values->newUntilTo) : NULL;
				$limitedUntilTo = $values->limitedUntilTo ? new \DateTime($values->limitedUntilTo) : NULL;
				$bestsellerUntilTo = $values->bestsellerUntilTo ? new \DateTime($values->bestsellerUntilTo) : NULL;
				$goodpriceUntilTo = $values->goodpriceUntilTo ? new \DateTime($values->goodpriceUntilTo) : NULL;
				$rareUntilTo = $values->rareUntilTo ? new \DateTime($values->rareUntilTo) : NULL;

        $product = $saveFacade->update((int)$product->getId(), $values->externalSystemId ? (int)$values->externalSystemId : NULL, $values->code, $values->stockState, $values->emptyStockState,
																			   $values->stock, $values->price, (float)$values->vat, $values->state, 
																					$newUntilTo, $limitedUntilTo, $bestsellerUntilTo, $goodpriceUntilTo, $rareUntilTo, 
																					TRUE, $values->completed === TRUE, $values->commentCompleted ?: NULL,
			Product::DEFAULT_TYPE, (bool)$values->discountAllowed);

        $productTranslation = $translationSaveFacade->update($translation->getId(), $values->name, $values->description, $this->getUrl($values),
            $this->getTitleSeo($values), $this->getDescriptionSeo($values), $values->shortDescription ?: null,
            $values->ogForm->titleOg ?? null, $values->ogForm->descriptionOg ?? null
        );

        $product->addTranslation($productTranslation);
        $this->saveMainPhoto($product, $values->photo, $saveFacade);

        return $product;
    }



    /**
     * @param Product $product
     * @param FileUpload $file
     * @param ProductSaveFacade $productSaveFacade
     * @return Product
     * @throws ProductSaveFacadeException
     */
    protected function saveMainPhoto(Product $product, FileUpload $file, ProductSaveFacade $productSaveFacade) : Product
    {
        if ($file->isOk()) {
            $productSaveFacade->savePhoto($product, $file);
        }
        return $product;
    }



    /**
     * @param Product $product
     * @param ArrayHash $values
     * @return Product
     * @throws ProductAdditionalPhotoSaveFacadeException
     */
    protected function saveAdditionalPhotos(Product $product, ArrayHash $values) : Product
    {
        if ($values->additionalPhotos) {
            $saveFacade = $this->productAdditionalPhotoSaveFacadeFactory->create();
            $saveFacade->add($product, $values->additionalPhotos);
        }

        return $product;
    }



    /**
     * @param $presenter Presenter
     * @param $exception \Exception
     * @return void
     */
    protected function sendErrorMessage(Presenter $presenter, \Exception $exception)
    {
        $presenter->flashMessage($exception->getMessage(), 'danger');
    }

    /****************** Helpers for get values ******************/

    /**
     * @param $values ArrayHash
     * @return string|null
     */
    protected function getUrl(ArrayHash $values)
    {
        return $values->{UrlFormContainer::NAME}->url ?? NULL;
    }



    /**
     * @param $values ArrayHash
     * @return string|null
     */
    protected function getTitleSeo(ArrayHash $values)
    {
        return $values->{SeoFormContainer::NAME}->titleSeo ?? NULL;
    }



    /**
     * @param $values ArrayHash
     * @return string|null
     */
    protected function getDescriptionSeo(ArrayHash $values)
    {
        return $values->{SeoFormContainer::NAME}->descriptionSeo ?? NULL;
    }
}
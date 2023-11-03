<?php

declare(strict_types = 1);

namespace App\Product\Translation;

use App\Language\LanguageEntity;
use App\Language\LanguageRepositoryFactory;
use App\Libs\FileManager\FileManager;
use App\NotFoundException;
use App\Product\Photo\PhotoManager;
use App\Product\ProductExistsAlreadyException;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepositoryFactory;
use App\Url\UrlResolver;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationSaveFacade extends NObject
{


    /** @var FileManager */
    protected $fileManager;

    /** @var LanguageRepositoryFactory */
    protected $languageRepoFactory;

    /** @var PhotoManager */
    protected $productPhotoManager;

    /** @var ProductRepositoryFactory */
    protected $productRepoFactory;

    /** @var ProductTranslationRepositoryFactory */
    protected $productTranslationRepoFactory;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(FileManager $fileManager,
                                LanguageRepositoryFactory $languageRepoFactory,
								PhotoManager $photoManager,
                                ProductRepositoryFactory $productRepoFactory,
                                ProductTranslationRepositoryFactory $productTranslationRepoFactory,
                                UrlResolver $urlResolver)
    {
        $this->fileManager = $fileManager;
        $this->languageRepoFactory = $languageRepoFactory;
        $this->productPhotoManager = $photoManager;
        $this->productRepoFactory = $productRepoFactory;
        $this->productTranslationRepoFactory = $productTranslationRepoFactory;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param int $productId
     * @param int $languageId
     * @param string $name
     * @param string|NULL $description
     * @param string|NULL $url
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
	 * @param $shortDescription string|null
     * @return ProductTranslation
     * @throws ProductTranslationSaveFacadeException
     */
    public function saveNew(
        int $productId,
        int $languageId,
        string $name,
        string $description = null,
        string $url = null,
        string $titleSeo = null,
        string $descriptionSeo = null,
        string $shortDescription = null,
        string $titleOg = null,
        string $descriptionOg = null
    ): ProductTranslation {
        try {
            $language = $this->checkLanguage($languageId);

            //check product
            $productRepo = $this->productRepoFactory->create();
            $product = $productRepo->getOneById($productId);

            $productTranslationFactory = new ProductTranslationFactory();
            $productTranslation = $productTranslationFactory->create($product->getId(), $language->getId(), $name, $description, $url,
                $titleSeo, $descriptionSeo, $titleOg, $descriptionOg);
            $productTranslation->setShortDescription($shortDescription);

            $translationRepo = $this->productTranslationRepoFactory->create();

            $productTranslation->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $translationRepo, $language->getId()));
            $this->checkDuplicate($productTranslation, $translationRepo);

            $translationRepo->save($productTranslation);

            return $productTranslation;

        } catch (NotFoundException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        } catch (ProductNotFoundException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        } catch (ProductExistsAlreadyException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param int $translationId
     * @param string $name
     * @param string|NULL $description
     * @param string|NULL $url
     * @param $titleSeo string|null
     * @param $descriptionSeo string|null
	 * @param $shortDescription string|null
     * @return ProductTranslation
     * @throws ProductTranslationSaveFacadeException
     */
    public function update(
        int $translationId,
        string $name,
        string $description = null,
        string $url = null,
        string $titleSeo = null,
        string $descriptionSeo = null,
        string $shortDescription = null,
        string $titleOg = null,
        string $descriptionOg = null
    ): ProductTranslation {
        try {
            $translationRepo = $this->productTranslationRepoFactory->create();
            $translation = $translationRepo->getOneById($translationId);

            $translation->setName($name);
            $translation->setDescription($description);
            $url !== $translation->getUrl() ? $translation->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $translationRepo, $translation->getLanguageId())) : NULL;
            $translation->setTitleSeo($titleSeo);
            $translation->setDescriptionSeo($descriptionSeo);
            $translation->setShortDescription($shortDescription);

            $translation->setTitleOg($titleOg);
            $translation->setDescriptionOg($descriptionOg);

            $this->checkDuplicate($translation, $translationRepo);

            $translationRepo->save($translation);

            return $translation;
        } catch (ProductTranslationNotFoundException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        } catch (ProductExistsAlreadyException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * Save settings for product search engines.
     * @param $translationId int
     * @param $googleMerchantTitle string|null
     * @return ProductTranslation
     * @throws ProductTranslationSaveFacadeException in case of error
     */
    public function saveProductSearchEngines(int $translationId, string $googleMerchantTitle = NULL) : ProductTranslation
    {
        try {
            $translationRepo = $this->productTranslationRepoFactory->create();
            $translation = $translationRepo->getOneById($translationId);
            $translation->setGoogleMerchantTitle($googleMerchantTitle);
            $translationRepo->save($translation);

            return $translation;
        } catch (ProductTranslationNotFoundException $exception) {
            throw new ProductTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ProductTranslation $productTranslation
     * @param ProductTranslationRepository $translationRepository
     * @return ProductTranslation
     * @throws ProductExistsAlreadyException
     */
    protected function checkDuplicate(ProductTranslation $productTranslation,
                                      ProductTranslationRepository $translationRepository) : ProductTranslation
    {
        $duplicateTranslation = $translationRepository->findOneByUrlAndLanguageId($productTranslation->getUrl(), $productTranslation->getLanguageId());

        $checker = new ProductTranslationDuplicate();
        $checker->checkUrl($productTranslation, $duplicateTranslation ?? NULL);

        return $productTranslation;
    }



    /**
     * @param int $languageId
     * @return LanguageEntity
     * @throws  NotFoundException
     */
    protected function checkLanguage(int $languageId) : LanguageEntity
    {
        $languageRepo = $this->languageRepoFactory->create();
        return $languageRepo->getOneById($languageId);
    }
}
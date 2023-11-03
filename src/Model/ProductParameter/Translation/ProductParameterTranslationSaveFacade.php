<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\Helpers\Entities;
use App\Language\LanguageRepositoryFactory;
use App\NotFoundException;
use App\Url\UrlResolver;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationSaveFacade extends NObject
{


    /** @var ProductParameterTranslationRepositoryFactory */
    protected $productParameterTranslationRepositoryFactory;

    /** @var ProductParameterRepositoryFactory */
    protected $productParameterRepositoryFactory;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(ProductParameterTranslationRepositoryFactory $productParameterTranslationRepositoryFactory,
                                ProductParameterRepositoryFactory $productParameterRepositoryFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory,
                                UrlResolver $urlResolver)
    {
        $this->productParameterTranslationRepositoryFactory = $productParameterTranslationRepositoryFactory;
        $this->productParameterRepositoryFactory = $productParameterRepositoryFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param ProductParameterEntity $productParameterEntity
     * @param int $languageId
     * @param string $value
     * @param $url string|null
     * @return ProductParameterTranslationEntity
     * @throws ProductParameterTranslationSaveFacadeException
     */
    public function add(ProductParameterEntity $productParameterEntity,
                        int $languageId,
                        string $value,
                        string $url = NULL) : ProductParameterTranslationEntity
    {
        try {
            //check language
            $languageRepository = $this->languageRepositoryFactory->create();
            $languageRepository->getOneById($languageId);

            //Repo
            $repo = $this->productParameterTranslationRepositoryFactory->create();

            //Create entity
            $entityFactory = new ProductParameterTranslationEntityFactory();
            $entity = $entityFactory->create($productParameterEntity->getId(), $languageId, $value, $url);
            $entity->setUrl($this->urlResolver->getAvailableUrl($url ?: $value, $repo, $languageId));

            //Check duplicate
            $this->checkDuplicate($productParameterEntity, $entity, $repo);

            //Save
            $repo->save($entity);

            return $entity;
        } catch (ProductParameterTranslationCheckDuplicateException $exception) {
            throw new ProductParameterTranslationSaveFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new ProductParameterTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ProductParameterEntity $productParameterEntity
     * @param ProductParameterTranslationEntity $productParameterTranslationEntity
     * @return ProductParameterTranslationEntity
     * @throws ProductParameterTranslationSaveFacadeException
     */
    public function update(ProductParameterEntity $productParameterEntity,
                           ProductParameterTranslationEntity $productParameterTranslationEntity) : ProductParameterTranslationEntity
    {
        try {
            //Repo
            $repo = $this->productParameterTranslationRepositoryFactory->create();
            $parameter = $repo->getOneById($productParameterTranslationEntity->getId());
            if ($parameter->getUrl() !== $productParameterTranslationEntity->getUrl()) {
                $productParameterTranslationEntity->setUrl($this->urlResolver->getAvailableUrl($productParameterTranslationEntity->getUrl() ?: $productParameterTranslationEntity->getValue(), $repo, $productParameterTranslationEntity->getLanguageId()));
            }

            //Check duplicate
            $this->checkDuplicate($productParameterEntity, $productParameterTranslationEntity, $repo);

            //Save
            $repo->save($productParameterTranslationEntity);

            return $productParameterTranslationEntity;
        } catch (ProductParameterTranslationCheckDuplicateException $exception) {
            throw new ProductParameterTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ProductParameterEntity $parameter
     * @param ProductParameterTranslationEntity $translation
     * @param ProductParameterTranslationRepository $parameterTranslationRepository
     * @return ProductParameterTranslationEntity
     * @throws ProductParameterTranslationCheckDuplicateException
     */
    protected function checkDuplicate(ProductParameterEntity $parameter,
                                      ProductParameterTranslationEntity $translation,
                                      ProductParameterTranslationRepository $parameterTranslationRepository)
    {
        //Load id of parameters of that group
        $repo = $this->productParameterRepositoryFactory->create();
        $parameters = $repo->findByProductParameterGroupId($parameter->getProductParameterGroupId());
        $parameters = $parameters ? Entities::getProperty($parameters, "id") : [];

        $duplicateTranslation = $parameterTranslationRepository->findOneByProductParameterIdAndLanguageIdAndValue($parameters ?: [],
            $translation->getLanguageId(), $translation->getValue());

        if ($duplicateTranslation) {
            $duplicateParameter = $repo->getOneById($duplicateTranslation->getProductParameterId());

            //check
            $duplicateCheck = new ProductParameterTranslationCheckDuplicate();
            $duplicateCheck->check($parameter, $translation, $duplicateParameter, $duplicateTranslation);
        }

        return $translation;
    }
}
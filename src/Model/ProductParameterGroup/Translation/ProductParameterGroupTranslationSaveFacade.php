<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use App\Language\LanguageRepositoryFactory;
use App\NotFoundException;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationSaveFacade extends Control
{


    /**
     * @var ProductParameterGroupTranslationRepositoryFactory
     */
    protected $productParameterGroupTranslationRepositoryFactory;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;



    public function __construct(ProductParameterGroupTranslationRepositoryFactory $productParameterGroupTranslationRepositoryFactory,
                                LanguageRepositoryFactory $languageRepositoryFactory)
    {
        parent::__construct();
        $this->productParameterGroupTranslationRepositoryFactory = $productParameterGroupTranslationRepositoryFactory;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
    }



    /**
     * @param ProductParameterGroupEntity $productParameterGroupEntity
     * @param int $languageId
     * @param string $name
     * @param string $filtrationTitle
     * @param $help string|null
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupTranslationSaveFacadeException
     */
    public function add(ProductParameterGroupEntity $productParameterGroupEntity,
                        int $languageId, string $name, string $filtrationTitle, string $help = NULL) : ProductParameterGroupTranslationEntity
    {
        try {
            //check language
            $languageRepo = $this->languageRepositoryFactory->create();
            $language = $languageRepo->getOneById($languageId);

            //Create translation
            $translationEntityFactory = new ProductParameterGroupTranslationEntityFactory();
            $translation = $translationEntityFactory->create($productParameterGroupEntity->getId(), $languageId, $name, $filtrationTitle);
            $translation->setHelp($help);

            //Translation repo
            $translationRepo = $this->productParameterGroupTranslationRepositoryFactory->create();

            //Check duplicate group
            $this->checkDuplicate($translationRepo, $translation);

            //Save
            $translationRepo->save($translation);

            return $translation;
        } catch (ProductParameterGroupTranslationCheckDuplicateException $exception) {
            throw new ProductParameterGroupTranslationSaveFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new ProductParameterGroupTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ProductParameterGroupTranslationEntity $translationEntity
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupTranslationSaveFacadeException
     */
    public function update(ProductParameterGroupTranslationEntity $translationEntity)
    : ProductParameterGroupTranslationEntity
    {
        try {
            //Translation repo
            $translationRepo = $this->productParameterGroupTranslationRepositoryFactory->create();

            //Check duplicate
            $this->checkDuplicate($translationRepo, $translationEntity);

            //Save
            $translationRepo->save($translationEntity);

            return $translationEntity;
        } catch (ProductParameterGroupTranslationCheckDuplicateException $exception) {
            throw new ProductParameterGroupTranslationSaveFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ProductParameterGroupTranslationRepository $repository
     * @param ProductParameterGroupTranslationEntity $entity
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupTranslationCheckDuplicateException
     */
    protected function checkDuplicate(ProductParameterGroupTranslationRepository $repository,
                                      ProductParameterGroupTranslationEntity $entity)
    : ProductParameterGroupTranslationEntity
    {

        $duplicateTranslation = $repository->findOneByLanguageIdAndName($entity->getLanguageId(), $entity->getName());
        $translationDuplicateCheck = new ProductParameterGroupTranslationCheckDuplicate();
        $translationDuplicateCheck->check($entity, ($duplicateTranslation ?: NULL));

        return $entity;
    }


}
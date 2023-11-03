<?php

namespace App\ArticleCategory;

use App\FacadeException;
use App\Helpers\Entities;
use App\Helpers\EntitiesException;
use App\Language\LanguageEntity;
use App\NotFoundException;
use App\Url\UrlResolver;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryFacade extends NObject
{


    /** @var ArticleCategoryCreateServiceFactory */
    protected $articleCategoryCreateServiceFactory;

    /** @var ArticleCategoryDuplicateServiceFactory */
    protected $articleCategoryDuplicateServiceFactory;

    /** @var ArticleCategoryRepositoryFactory */
    protected $articleCategoryRepositoryFactory;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(ArticleCategoryCreateServiceFactory $articleCategoryCreateServiceFactory,
                                ArticleCategoryRepositoryFactory $articleCategoryRepositoryFactory,
                                ArticleCategoryDuplicateServiceFactory $articleCategoryDuplicateServiceFactory,
                                UrlResolver $urlResolver)
    {
        $this->articleCategoryCreateServiceFactory = $articleCategoryCreateServiceFactory;
        $this->articleCategoryRepositoryFactory = $articleCategoryRepositoryFactory;
        $this->articleCategoryDuplicateServiceFactory = $articleCategoryDuplicateServiceFactory;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param $lang int|LanguageEntity
     * @param $moduleId int
     * @param $name string
     * @param $url string|null
     * @param $titleSeo string|null
     * @param $descriptionSeo string|null
     * @return ArticleCategoryEntity
     * @throws  FacadeException
     */
    public function add($lang, int $moduleId, $name, $url = null, $titleSeo = null, $descriptionSeo = null)
    {
        try {
            $repo = $this->articleCategoryRepositoryFactory->create();
            $articleCategoryEntity = $this->articleCategoryCreateServiceFactory->create()->createEntity($lang->getId(), $moduleId, $name, $url, $titleSeo, $descriptionSeo);
            $articleCategoryEntity->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $repo, (int)$lang->getId()));
            $duplicateService = $this->articleCategoryDuplicateServiceFactory->create();
            $duplicateService->checkName($articleCategoryEntity, $repo->findOneByNameAndLangId($articleCategoryEntity->getName(), $lang->getId()));
            $duplicateService->checkUrl($articleCategoryEntity, $repo->findOneByUrlAndLangId($articleCategoryEntity->getUrl(), $lang->getId()));
            $repo->save($articleCategoryEntity);
            return $articleCategoryEntity;
        } catch (ArticleCategoryDuplicateServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * @param $articleCategoryEntity ArticleCategoryEntity
     * @return ArticleCategoryEntity
     * @throws FacadeException
     */
    public function save(ArticleCategoryEntity $articleCategoryEntity)
    {
        if (!$articleCategoryEntity->getId()) {
            throw new FacadeException("For a new article category use 'add()' method.");
        }
        try {
            $repo = $this->articleCategoryRepositoryFactory->create();
            $duplicateService = $this->articleCategoryDuplicateServiceFactory->create();
            $articleCategory = $repo->getOneById($articleCategoryEntity->getId());
            if ($articleCategory->getUrl() !== $articleCategoryEntity->getUrl()) {
                $articleCategoryEntity->setUrl($this->urlResolver->getAvailableUrl($articleCategoryEntity->getUrl() ?: $articleCategoryEntity->getName(), $repo, $articleCategoryEntity->getLanguageId()));
            }
            $duplicateService->checkName($articleCategoryEntity, $repo->findOneByNameAndLangId($articleCategoryEntity->getName(), $articleCategoryEntity->getLanguageId()));
            $duplicateService->checkUrl($articleCategoryEntity, $repo->findOneByUrlAndLangId($articleCategoryEntity->getUrl(), $articleCategoryEntity->getLanguageId()));
            $repo->save($articleCategoryEntity);
            return $articleCategoryEntity;
        } catch (ArticleCategoryDuplicateServiceException $exception) {
            throw new FacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * @param ArticleCategoryEntity[] $entities
     * @param $id array
     * @return ArticleCategoryEntity[]
     * @throws FacadeException
     */
    public function sort(array $entities, array $id)
    {
        try {
            Entities::hasId($entities);
            $entitiesSorted = Entities::sortById(($entities ? $entities : []), $id, TRUE);
            $repo = $this->articleCategoryRepositoryFactory->create();
            $repo->save($entitiesSorted);
            return $entitiesSorted;
        } catch (EntitiesException $exception) {
            throw new FacadeException($exception->getMessage());
        }
    }



    /**
     * @param $articleCategoryEntity ArticleCategoryEntity
     * @return int
     */
    public function remove(ArticleCategoryEntity $articleCategoryEntity)
    {
        return $this->articleCategoryRepositoryFactory->create()->remove($articleCategoryEntity);
    }
}
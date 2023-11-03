<?php

declare(strict_types = 1);

namespace App\Article;

use App\Article\Module\ModuleRepository;
use App\ArticleCategory\ArticleCategoryRepository;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipRepository;
use App\Helpers\Entities;
use App\Language\LanguageRepositoryFactory;
use App\NotFoundException;
use App\Page\PageEntity;
use App\Page\PageRepository;
use App\Url\UrlResolver;
use Nette\Http\FileUpload;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleFacade extends NObject
{


    /** @var ArticleCategoryRelationshipRepository */
    protected $articleCategoryRelationRepo;

    /** @var ArticleCategoryRepository */
    protected $articleCategoryRepo;

    /** @var LanguageRepositoryFactory */
    protected $languageRepositoryFactory;

    /** @var ArticleRepositoryFactory */
    protected $articleRepositoryFactory;

    /** @var ArticleCoverPhotoServiceFactory */
    protected $articleCoverPhotoServiceFactory;

    /** @var ModuleRepository */
    protected $moduleRepo;

    /** @var PageRepository */
    protected $pageRepo;

    /** @var UrlResolver */
    protected $urlResolver;



    public function __construct(ArticleCategoryRelationshipRepository $articleCategoryRelationshipRepository,
                                ArticleCategoryRepository $articleCategoryRepository,
                                LanguageRepositoryFactory $languageRepositoryFactory,
                                ArticleRepositoryFactory $articleRepositoryFactory,
                                ArticleCoverPhotoServiceFactory $articleCoverPhotoServiceFactory,
                                ModuleRepository $moduleRepository,
                                PageRepository $pageRepository,
                                UrlResolver $urlResolver)
    {
        $this->articleCategoryRelationRepo = $articleCategoryRelationshipRepository;
        $this->articleCategoryRepo = $articleCategoryRepository;
        $this->languageRepositoryFactory = $languageRepositoryFactory;
        $this->articleRepositoryFactory = $articleRepositoryFactory;
        $this->articleCoverPhotoServiceFactory = $articleCoverPhotoServiceFactory;
        $this->moduleRepo = $moduleRepository;
        $this->pageRepo = $pageRepository;
        $this->urlResolver = $urlResolver;
    }



    /**
     * @param $langId int
     * @param $name string
     * @param $introduction string
     * @param $content string
     * @param $status string
     * @param $url string
     * @param $titleSeo string
     * @param $descriptionSeo string
     * @param $coverPhoto null|FileUpload
     * @return ArticleEntity
     * @throws ArticleFacadeException
     */
    public function add($langId,
                        $name,
                        $introduction,
                        $content,
                        $status,
                        $url = null,
                        $titleSeo = null,
                        $descriptionSeo = null,
                        $coverPhoto = null
    )
    {
        try {
            if ($coverPhoto instanceof FileUpload && $coverPhoto->hasFile()) {
                $coverPhoto = $this->articleCoverPhotoServiceFactory->create()->upload($coverPhoto);
            }

            $this->languageRepositoryFactory->create()->getOneById($langId);
            $createService = new ArticleCreateService();
            $entity = $createService->createEntity($langId, $name, $url, $titleSeo, $descriptionSeo,
                $coverPhoto, $introduction, $content, $status);
            $repo = $this->articleRepositoryFactory->create();
            $entity->setUrl($this->urlResolver->getAvailableUrl($url ?: $name, $repo, (int)$langId));
            $duplicateService = new ArticleDuplicateService();
            $duplicateService->checkName($entity, $repo->findOneByNameAndLangId($entity->getName(), $langId));
            $duplicateService->checkUrl($entity, $repo->findOneByUrlAndLangId($entity->getUrl(), $langId));
            $repo->save($entity);

            return $entity;
        } catch (NotFoundException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        } catch (ArticleDuplicateServiceException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        } catch (ArticleCoverPhotoServiceException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $entity ArticleEntity
     * @return ArticleEntity
     * @throws ArticleFacadeException
     */
    public function update(ArticleEntity $entity)
    {
        if (!$entity->getId()) {
            throw new ArticleFacadeException("For a new article use 'add()' method.");
        }
        try {
            if ($entity->getCoverPhoto() instanceof FileUpload && $entity->getCoverPhoto()->hasFile()) {
                $entity->setCoverPhoto($this->articleCoverPhotoServiceFactory->create()->upload($entity->getCoverPhoto()));
            }
            $repo = $this->articleRepositoryFactory->create();
            $articleFromDb = $repo->getOneById($entity->getId());
            if ($entity->getUrl() !== $articleFromDb->getUrl()) {
                $entity->setUrl($this->urlResolver->getAvailableUrl($entity->getUrl() ?: $entity->getName(), $repo, (int)$entity->getLanguageId()));
            }

            $duplicateService = new ArticleDuplicateService();
            $duplicateService->checkName($entity, $repo->findOneByNameAndLangId($entity->getName(), $entity->getLanguageId()));
            $duplicateService->checkUrl($entity, $repo->findOneByUrlAndLangId($entity->getUrl(), $entity->getLanguageId()));
            $repo->save($entity);

            return $entity;
        } catch (ArticleDuplicateServiceException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        } catch (ArticleCoverPhotoServiceException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        } catch (NotFoundException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return int
     * @throws ArticleFacadeException
     */
    public function remove(int $id) : int
    {
        try {
            $repo = $this->articleRepositoryFactory->create();
            $article = $repo->getOneById($id);
            return $repo->remove($article);
        } catch (NotFoundException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ArticleDTO
     * @throws ArticleFacadeException
     * todo test
     * todo remove, because unnecessary ArticleDTO because ArticleEntity object loads category relations and category relations load categories
     */
    public function getOnePublishedByUrlAndLanguageId(string $url, int $languageId) : ArticleDTO
    {
        try {
            $article = $this->articleRepositoryFactory->create()->getOnePublishedByUrlAndLanguageId($url, $languageId);
            $articleCategoryRelations = $this->articleCategoryRelationRepo->findByArticleId($article->getId());
            $articleCategories = $articleCategoryRelations ? $this->articleCategoryRepo->findByMoreId(Entities::getProperty($articleCategoryRelations, 'articleCategoryId')) : [];
            $modules = $articleCategories ? $this->moduleRepo->findByMoreId(Entities::getProperty($articleCategories, 'moduleId')) : [];
            $modules = $modules ? Entities::setIdAsKey($modules) : [];
            $pages = $modules ? $this->pageRepo->findArticlesTypeByMoreArticleModuleId(Entities::getProperty($modules, 'id')) : [];
            $pages = $pages ? Entities::setValueAsKey($pages, 'articleModuleId') : [];

            $articleDTO = new ArticleDTO($article);
            foreach ($articleCategories as $articleCategory) {
                $module = $modules[$articleCategory->getModuleId()];
                $page = $pages[$module->getId()] ?? NULL;
                $page ? $module->setPage($page) : NULL;
                $articleCategory->setModule($module);
                $articleDTO->addCategory($articleCategory);
            }
            return $articleDTO;
        } catch (NotFoundException $exception) {
            throw new ArticleFacadeException($exception->getMessage());
        }
    }



    /**
     * @param $id int
     * @return PageEntity[]|array
     * todo test
     */
    public function findPublishedModulePagesByArticleId(int $id) : array
    {
        $articleCategoryRelations = $this->articleCategoryRelationRepo->findByArticleId($id);
        $categories = $articleCategoryRelations ? $this->articleCategoryRepo->findByMoreId(Entities::getProperty($articleCategoryRelations, 'articleCategoryId')) : [];
        return $categories ? $this->pageRepo->findPublishedByMoreModuleId(Entities::getProperty($categories, 'moduleId')) : [];
    }
}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleFacadeException extends \Exception
{


}
<?php

namespace App\ArticleCategoryRelationship;

use App\Article\ArticleEntity;
use App\EntitySavedCompare;
use App\Helpers\Entities;
use App\NotFoundException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRelationshipFacade extends NObject
{


    /** @var ArticleCategoryRelationshipRepositoryFactory */
    protected $articleCategoryRelationshipRepositoryFactory;

    /** @var ArticleCategoryRelationshipSetServiceFactory */
    protected $articleCategoryRelationshipSetServiceFactory;



    public function __construct(ArticleCategoryRelationshipRepositoryFactory $articleCategoryRelationshipRepositoryFactory,
                                ArticleCategoryRelationshipSetServiceFactory $articleCategoryRelationshipSetServiceFactory)
    {
        $this->articleCategoryRelationshipRepositoryFactory = $articleCategoryRelationshipRepositoryFactory;
        $this->articleCategoryRelationshipSetServiceFactory = $articleCategoryRelationshipSetServiceFactory;
    }



    /**
     * @param ArticleEntity $articleEntity
     * @param array $categoryId
     * @return ArticleCategoryRelationshipEntity[]
     * @throws ArticleCategoryRelationshipFacadeException
     */
    public function add(ArticleEntity $articleEntity, array $categoryId)
    {
        try {
            $repo = $this->articleCategoryRelationshipRepositoryFactory->create();
            $service = new ArticleCategoryRelationshipCreateService();
            $entities = [];
            foreach ($categoryId as $id) {
                $entities[] = $service->create($articleEntity->getId(), $id);
            }
            $repo->save($entities);
            return $entities;
        } catch (NotFoundException $exception) {
            throw new ArticleCategoryRelationshipFacadeException($exception->getMessage());
        }
    }



    /**
     * @param ArticleEntity $articleEntity
     * @param array|null $categoryId
     */
    public function update(ArticleEntity $articleEntity, array $categoryId = null)
    {
        $repo = $this->articleCategoryRelationshipRepositoryFactory->create();
        $categories = $repo->findByArticleId($articleEntity->getId());

        $result = Entities::searchValues($categories, $categoryId, "articleCategoryId");
        if (isset($result[Entities::VALUE_NOT_FOUND])) {
            $entities = [];
            $service = new ArticleCategoryRelationshipCreateService();
            foreach ($result[Entities::VALUE_NOT_FOUND] as $catId) {
                $entities[] = $service->create($articleEntity->getId(), $catId);
            }
            $repo->save($entities);
        }
        if (isset($result[Entities::ENTITY_WITHOUT_VALUE])) {
            $repo->remove($result[Entities::ENTITY_WITHOUT_VALUE]);
        }
    }



    /**
     * @param $categoryId int
     * @param $newCategoryId int
     * @return int
     */
    public function replaceCategory($categoryId, $newCategoryId)
    {
        $i = 0;
        $repo = $this->articleCategoryRelationshipRepositoryFactory->create();
        $categories = $repo->findByArticleCategoryId($categoryId);
        $service = $this->articleCategoryRelationshipSetServiceFactory->create();
        foreach ($categories ? $categories : [] as $category) {
            $service->setArticleCategoryId($category, $newCategoryId);
            $repo->save($category);
            $i++;
        }
        return $i;
    }


}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRelationshipFacadeException extends \Exception
{


}
<?php

namespace App\Article;

use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;
use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleRepository extends BaseRepository implements IRepositorySource, IRepository, IUrlRepository
{


    /** @var string */
    protected $entityName = ArticleEntity::class;



    /**
     * @param $id int
     * @return ArticleEntity
     * @throws NotFoundException
     */
    public function getOneById(int $id)
    {
        $filters['where'][] = ['id', '=', $id];
        $result = $this->findOneBy($filters);
        if (!$result) {
            throw new NotFoundException("Článek nebyl nalezen.");
        }
        return $result;
    }



    /**
     * Get one published article by id.
     * @param $id int
     * @return ArticleEntity
     * @throws NotFoundException
     */
    public function getOnePublishedById(int $id)
    {
        $filters['where'][] = ['id', '=', $id];
        $filters['where'][] = $this->getPublishCondition(TRUE);
        $article = $this->findOneBy($filters);
        if (!$article) {
            throw new NotFoundException('Článek nebyl nalezen.');
        }
        return $article;
    }



    /**
     * @param $categoryId array
     * @param $limit int
     * @return ArticleEntity[]|array
     */
    public function findLastPublishedByMoreCategoryId(array $categoryId, int $limit = 3) : array
    {
        $categoryRelation = ArticleCategoryRelationshipEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\'))',
            $categoryRelation->getPropertyByName('articleId')->getColumn()->getName(),
            $categoryRelation->getTable()->getName(),
            $categoryRelation->getPropertyByName('articleCategoryId')->getColumn()->getName(),
            implode('\',\'', $categoryId));

        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        $filter['where'][] = ['status', '=', ArticleEntity::PUBLISH];
        $filter['sort'] = ['addDate', 'DESC'];
        $filter['limit'] = $limit;
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $categoryId array
     * @return ArticleEntity|null
     */
    public function findOneLastPublishedByMoreCategoryId(array $categoryId)
    {
        $categoryRelation = ArticleCategoryRelationshipEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\'))',
            $categoryRelation->getPropertyByName('articleId')->getColumn()->getName(),
            $categoryRelation->getTable()->getName(),
            $categoryRelation->getPropertyByName('articleCategoryId')->getColumn()->getName(),
            implode('\',\'', $categoryId));

        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        $filter['where'][] = ['status', '=', ArticleEntity::PUBLISH];
        $filter['sort'] = ['addDate', 'DESC'];
        $filter['limit'] = 1;
        return $this->findOneBy($filter) ?: NULL;
    }

		
		
    public function findOneByUrlAndLangId($url, $langId)
    {
        if ($url && $langId) {
            return $this->findOneBy([
                "where" => [
                    ["url", "=", $url],
                    ["languageId", "=", $langId]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ArticleEntity|null
    */
    public function findOneByUrlAndLanguageId(string $url, int $languageId)
    {
        return $this->findOneByUrlAndLangId($url, $languageId) ?: NULL;
    }



    public function findOneByNameAndLangId($name, $langId)
    {
        if ($name && $langId) {
            return $this->findOneBy([
                "where" => [
                    ["name", "=", $name],
                    ["languageId", "=", $langId]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ArticleEntity
     * @throws NotFoundException
     */
    public function getOnePublishedByUrlAndLanguageId(string $url, int $languageId) : ArticleEntity
    {
        $filter['where'][] = ['url', '=', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['status', '=', ArticleEntity::PUBLISH];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new NotFoundException('Article not found.');
        }
        return $result;
    }



    /**
     * @param $categoryId array
     * @param $offset int
     * @param $limit int
     * @return ArticleEntity[]|array
     */
    public function findPublishedByMoreCategoryIdAndOffsetAndLimit(array $categoryId, int $offset, int $limit) : array
    {
        $categoryRelation = ArticleCategoryRelationshipEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\'))',
            $categoryRelation->getPropertyByName('articleId')->getColumn()->getName(),
            $categoryRelation->getTable()->getName(),
            $categoryRelation->getPropertyByName('articleCategoryId')->getColumn()->getName(),
            implode('\',\'', $categoryId));

        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        $filter['where'][] = ['status', '=', ArticleEntity::PUBLISH];
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        $filter['sort'] = ['addDate', 'DESC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $categoryId array
     * @return CountDTO
     */
    public function countPublishedByMoreCategoryId(array $categoryId) : CountDTO
    {
        $categoryRelation = ArticleCategoryRelationshipEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\'))',
            $categoryRelation->getPropertyByName('articleId')->getColumn()->getName(),
            $categoryRelation->getTable()->getName(),
            $categoryRelation->getPropertyByName('articleCategoryId')->getColumn()->getName(),
            implode('\',\'', $categoryId));

        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        $filter['where'][] = ['status', '=', ArticleEntity::PUBLISH];
        return $this->count($filter);
    }



    /**
     * @param $languageId int
     * @param $query string
     * @return CountDTO
     */
    public function countBySearch(int $languageId, string $query) : CountDTO
    {
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['name', 'LIKE', '%' . $query . '%'];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->count($filter);
    }



    /**
     * @param $languageId int
     * @param $query string
     * @param $limit int
     * @param $offset int
     * @return ArticleEntity[]|array
     */
    public function findBySearch(int $languageId, string $query, int $limit, int $offset) : array
    {
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['where'][] = ['name', 'LIKE', '%' . $query . '%'];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id int
     * @param $limit int
     * @return ArticleEntity[]|array
    */
    public function findPublishedRelatedByArticleId(int $id, int $limit = 3) : array
    {
        //todo replace column names by data from annotation
        $subQuery = sprintf('(SELECT acr_article_id
                            FROM article_category_relationship
                            WHERE acr_article_category_id IN
                                (SELECT acr_article_category_id
                                FROM article_category_relationship
                                WHERE acr_article_id = \'%d\') )', $id);

        $filter['limit'] = $limit;
        $filter['where'][] = ['id', '!=', $id]; //exclude yourself
        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        $filter['where'][] = $this->getPublishCondition(TRUE);
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $publish bool
     * @return array
     */
    protected function getPublishCondition($publish)
    {
        return $publish === TRUE ? ["status", "=", ArticleEntity::PUBLISH] : [];
    }
}

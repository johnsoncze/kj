<?php

namespace App\ArticleCategory;

use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;
use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use App\Url\IUrlRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRepository extends BaseRepository implements IRepositorySource, IRepository, IUrlRepository
{


    /** @var string */
    protected $entityName = ArticleCategoryEntity::class;



    /**
     * @param $filters array
     * @return array|\IteratorAggregate[]
     * @todo změnit načítání dat. musejí jít přes nějaký mapper jinak při nějaké modifikaci dat
     * @todo nebude aplikovaná logika, např. html_entity_decode(..)
     */
    public function findForList(array $filters)
    {
        $articleCategoryAnnotation = ArticleCategoryEntity::getAnnotation();
        $articleCategoryRelationshipAnnotation = ArticleCategoryRelationshipEntity::getAnnotation();

        $select = "(SELECT count(*) FROM {$articleCategoryRelationshipAnnotation->getTable()->getName()} 
        WHERE {$articleCategoryRelationshipAnnotation->getPropertyByName('articleCategoryId')->getColumn()->getName()} = {$articleCategoryAnnotation->getPrimaryProperty()->getColumn()->getName()}
        ) AS articlesCount ";

        $filters["columns"] = [
            $articleCategoryAnnotation->getPropertyByName("id")->getColumn()->getName() . " AS id",
            $articleCategoryAnnotation->getPropertyByName("name")->getColumn()->getName() . " AS name",
            $articleCategoryAnnotation->getPropertyByName("languageId")->getColumn()->getName() . " AS languageId",
            $articleCategoryAnnotation->getPropertyByName("moduleId")->getColumn()->getName() . " AS moduleId",
            $articleCategoryAnnotation->getPropertyByName("addDate")->getColumn()->getName() . " AS addDate",
            $select
        ];

        $result = $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, function ($result) {
                return $result;
            });
        return $result ? $result : [];
    }



    /**
     * @param $moduleId int
     * @param $languageId int
     * @return array
     */
    public function findByModuleIdAndLanguageId(int $moduleId, int $languageId) : array
    {
        $filter['where'][] = ['moduleId', '=', $moduleId];
        $filter['where'][] = ['languageId', '=', $languageId];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    public function findByLanguageId($languageId)
    {
        if ($languageId) {
            return $this->findBy([
                "where" => [
                    ["languageId", "=", $languageId]
                ], "sort" => [['LENGTH(sort)', 'sort'], "ASC"],
            ]);
        }
        return null;
    }



    /**
     * @param $langId int
     * @param $moduleId int
     * @return ArticleCategoryEntity[]|array
     */
    public function findByLanguageIdAndModuleId(int $langId, int $moduleId) : array
    {
        $filter['where'][] = ['languageId', '=', $langId];
        $filter['where'][] = ['moduleId', '=', $moduleId];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
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
     * @return ArticleCategoryEntity
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



    public function findByMoreId(array $id = null)
    {
        if ($id) {
            return $this->findBy([
                "where" => [
                    ["id", "", $id]
                ]
            ]);
        }
        return null;
    }



    /**
     * @param $id int
     * @return ArticleCategoryEntity
     * @throws NotFoundException
     */
    public function getOneById(int $id)
    {
        if ($id) {
            $result = $this->findOneBy([
                "where" => [
                    ["id", "=", $id]
                ]
            ]);
            if ($result) {
                return $result;
            }
        }
        throw new NotFoundException("Rubrika nebyla nalezena.");
    }



    /**
     * @param $url string
     * @param $languageId int
     * @return ArticleCategoryEntity
     * @throws NotFoundException
     */
    public function getOneByUrlAndLanguageId(string $url, int $languageId) : ArticleCategoryEntity
    {
        $filter['where'][] = ['url', '=', $url];
        $filter['where'][] = ['languageId', '=', $languageId];
        $result = $this->findOneBy($filter);
        if (!$result) {
            throw new NotFoundException("Rubrika nebyla nalezena.");
        }
        return $result;
    }
}
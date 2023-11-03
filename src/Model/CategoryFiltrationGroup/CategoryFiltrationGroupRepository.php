<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\Category\CategoryEntity;
use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = CategoryFiltrationGroupEntity::class;



    /**
     * @param int $id
     * @return CategoryFiltrationGroupEntity
     * @throws CategoryFiltrationGroupNotFoundException
     */
    public function getOneById(int $id)
    {
        $result = $this->findOneBy([
            "where" => [
                ["id", "=", $id]
            ]
        ]);
        if (!$result) {
            throw new CategoryFiltrationGroupNotFoundException(sprintf("Group of category filtration with id '%s' not found.", $id));
        }
        return $result;
    }



    /**
     * @param $languageId int
     * @return CountDTO
     */
    public function getCountIndexedByLanguageId(int $languageId) : CountDTO
    {
        $filter['where'][] = ['categoryId', 'IN.SQL', '(' . $this->getQueryForCategoryIdByLanguageId($languageId) . ')'];
        $filter['where'][] = ['indexSeo', '=', TRUE];
        $filter['sort'] = ['titleSeo', 'ASC'];
        return $this->count($filter);
    }



    /**
     * @param $languageId int
     * @param $limit int
     * @param $offset int
     * @return CategoryFiltrationGroupEntity[]|array
     */
    public function findIndexedByLanguageIdAndLimitAndOffset(int $languageId, int $limit, int $offset) : array
    {
        $filter['where'][] = ['categoryId', 'IN.SQL', '(' . $this->getQueryForCategoryIdByLanguageId($languageId) . ')'];
        $filter['where'][] = ['indexSeo', '=', TRUE];
        $filter['sort'] = ['titleSeo', 'ASC'];
        $filter['limit'] = $limit;
        $filter['offset'] = $offset;
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $id array
     * @return CategoryFiltrationGroupEntity[]|array
    */
    public function findIndexedByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        $filter['where'][] = ['indexSeo', '=', TRUE];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param array $id
     * @return CategoryFiltrationGroupEntity[]|null
     */
    public function findById(array $id)
    {
        $filter['where'][] = ['id', '', $id];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param int $categoryId
     * @return mixed
     */
    public function findByCategoryId(int $categoryId)
    {
        $filter['where'][] = ['categoryId', '=', $categoryId];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $categoryId array
     * @return CategoryFiltrationGroupEntity[]|array
     */
    public function findByMoreCategoryIdForMenu(array $categoryId) : array
    {
        $filter['where'][] = ['categoryId', '', $categoryId];
        $filter['where'][] = ['showInMenu', '=', TRUE];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param array $groupsId
     * @param int $categoryId
     * @return mixed
     */
    public function findByGroupsIdAndCategoryId(array $groupsId, int $categoryId)
    {
        return $this->findBy([
            'where' => [
                ['id', '', $groupsId],
                ['categoryId', '=', $categoryId]
            ]
        ]);
    }



    /**
     * @param int $categoryId
     * @param int $categoryFiltrationGroupId
     * @return mixed
     */
    public function findByCategoryWithoutCategoryFiltrationGroupId(int $categoryId,
                                                                   int $categoryFiltrationGroupId)
    {
        return $this->findBy([
            "where" => [
                ["categoryId", "=", $categoryId],
                ["id", "!=", $categoryFiltrationGroupId]
            ]
        ]);
    }



    /**
     * @param $id array
     * @return CategoryFiltrationGroupEntity[]|array
    */
    public function findByMoreId(array $id) : array
    {
        $filter['where'][] = ['id', '', $id];
        return $this->findBy($filter) ?: [];
    }



    /**
     * Find group which has exactly parameters and no other.
     * @param $categoryId int
     * @param $parameterId array
     * @return CategoryFiltrationGroupEntity|null
     */
    public function findOneByCategoryIdAndMoreParameterId(int $categoryId, array $parameterId)
    {
        $i = 1;
        $subSubQuery = '';
        foreach ($parameterId as $id) {
            $nextParameterId = $i > 1;
            $subSubQuery .= $nextParameterId ? ' AND cfgi_category_filtration_group_id IN ( ' : '';
            $subSubQuery .= sprintf('SELECT cfgi_category_filtration_group_id FROM category_filtration_group_parameter WHERE cfgi_product_parameter_id = \'%d\'', $id);
            $subSubQuery .= $nextParameterId ? ')' : '';
            $i++;
        }

        $subQuery = sprintf('(SELECT cfgi_category_filtration_group_id 
                    FROM category_filtration_group_parameter 
                    WHERE cfgi_category_filtration_group_id IN (%s) 
                    GROUP BY cfgi_category_filtration_group_id HAVING COUNT(*) = \'%d\')',
            $subSubQuery,
            count($parameterId));

        $filter['where'][] = ['categoryId', '=', $categoryId];
        $filter['where'][] = ['id', 'IN.SQL', $subQuery];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $languageId int
     * @return string
     */
    private function getQueryForCategoryIdByLanguageId(int $languageId) : string
    {
        $categoryAnnotation = CategoryEntity::getAnnotation();
        return sprintf('SELECT %s FROM %s WHERE %s = %s AND %s = \'%s\'',
            $categoryAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $categoryAnnotation->getTable()->getName(),
            $categoryAnnotation->getPropertyByName('languageId')->getColumn()->getName(),
            $languageId,
            $categoryAnnotation->getPropertyByName('status')->getColumn()->getName(),
            CategoryEntity::PUBLISH);
    }
}
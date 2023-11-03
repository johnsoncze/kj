<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroupParameter;

use App\Category\CategoryEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\IRepository;
use App\Product\Parameter\ProductParameter;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupParameterRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = CategoryFiltrationGroupParameterEntity::class;



    /**
     * @param int $id
     * @return mixed
     */
    public function findByCategoryFiltrationGroupId(int $id)
    {
        return $this->findBy([
            "where" => [
                ["categoryFiltrationGroupId", "=", $id]
            ]
        ]);
    }



    /**
     * @param array $id
     * @return mixed
     */
    public function findByProductParametersId(array $id)
    {
        return $this->findBy([
            'where' => [
                ['productParameterId', '', $id]
            ]
        ]);
    }



    /**
     * @param int $parameterId
     * @return mixed
     */
    public function findByProductParameterId(int $parameterId)
    {
        return $this->findBy([
            'where' => [
                ['productParameterId', '=', $parameterId]
            ]
        ]);
    }



    /**
     * By product parameters find similar groups.
     * @param $productId int
     * @param $languageId int
     * @param $limit int
     * @return array
     */
    public function findSimilarGroupsIdForPublishedCategoriesByProductIdAndLanguageId(int $productId,
                                                                                      int $languageId,
                                                                                      int $limit) : array
    {
        //productParameterId subquery
        $productParameterAnnotation = ProductParameter::getAnnotation();
        $productParameterIdSubqery = sprintf('(SELECT %s FROM %s WHERE %s = %d)',
            $productParameterAnnotation->getPropertyByName('parameterId')->getColumn()->getName(),
            $productParameterAnnotation->getTable()->getName(),
            $productParameterAnnotation->getPropertyByName('productId')->getColumn()->getName(),
            $productId);

        //categoryFiltrationGroupId subquery
        $categoryAnnotation = CategoryEntity::getAnnotation();
        $categoryFiltrationGroupAnnotation = CategoryFiltrationGroupEntity::getAnnotation();
        $categoryFiltrationGroupIdSubquery = sprintf('(SELECT %s FROM %s WHERE %s IN 
        (SELECT %s FROM %s WHERE %s = %d AND %s = \'%s\'))',
            $categoryFiltrationGroupAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $categoryFiltrationGroupAnnotation->getTable()->getName(),
            $categoryFiltrationGroupAnnotation->getPropertyByName('categoryId')->getColumn()->getName(),
            $categoryAnnotation->getPropertyByName('id')->getColumn()->getName(),
            $categoryAnnotation->getTable()->getName(),
            $categoryAnnotation->getPropertyByName('languageId')->getColumn()->getName(),
            $languageId,
            $categoryAnnotation->getPropertyByName('status')->getColumn()->getName(),
            CategoryEntity::PUBLISH);

        $filter['limit'] = $limit;
        $filter['sort'] = ['COUNT(*)', 'DESC'];
        $filter['group'] = ['categoryFiltrationGroupId'];
        $filter['columns'] = ['categoryFiltrationGroupId'];
        $filter['where'][] = ['productParameterId', 'IN.SQL', $productParameterIdSubqery];
        $filter['where'][] = ['categoryFiltrationGroupId', 'IN.SQL', $categoryFiltrationGroupIdSubquery];

        return $this->getEntityMapper()
            ->getQueryManager(CategoryFiltrationGroupParameterEntity::class)
            ->findBy($filter, function ($rows) {
                $response = [];
                $groupParameterAnnotation = CategoryFiltrationGroupParameterEntity::getAnnotation();
                foreach ($rows as $row) {
                    $response[] = $row[$groupParameterAnnotation->getPropertyByName('categoryFiltrationGroupId')->getColumn()->getName()];
                }
                return $response;
            }) ?: [];
    }



    /**
     * @param array $groupsId
     * @param array $parametersId
     * @return mixed
     */
    public function findByCategoryFiltrationGroupsIdAndProductParametersId(array $groupsId, array $parametersId)
    {
        $sum = array_sum($parametersId);
        $annotation = CategoryFiltrationGroupParameterEntity::getAnnotation();
        $categoryFiltrationGroupId = $annotation->getPropertyByName("categoryFiltrationGroupId");
        $productParameterId = $annotation->getPropertyByName("productParameterId");

        return $this->findBy([
            "where" => [
                ["categoryFiltrationGroupId", "", $groupsId],
                "EXISTS(SELECT * FROM `{$annotation->getTable()->getName()}` AS `t2`
                WHERE `t2`.{$categoryFiltrationGroupId->getColumn()->getName()} 
                = {$categoryFiltrationGroupId->getColumn()->getName()} 
                AND `t2`.{$productParameterId->getColumn()->getName()} IN (" . implode(",", $parametersId) . "))",
            ], "group" =>
                ["categoryFiltrationGroupId"],
            "having" => [
                ["SUM(productParameterId)", "=", $sum]
            ]
        ]);
    }
}
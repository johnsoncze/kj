<?php

declare(strict_types = 1);

namespace App\CategoryProductParameter;

use App\Extensions\Grido\IRepositorySource;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryProductParameterRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = CategoryProductParameterEntity::class;



    /**
     * @param int $categoryId
     * @return CategoryProductParameterEntity[]
     */
    public function findByCategoryId(int $categoryId)
    {
        return $this->findBy([
            "where" => [
                ["categoryId", "=", $categoryId]
            ]
        ]);
    }



    /**
     * @param $parameterId array
     * @return CategoryProductParameterEntity[]|array
     */
    public function findWhichContainAtLeastOneOfMoreParameterId(array $parameterId) : array
    {
        $categoryProductParameterAnnotation = CategoryProductParameterEntity::getAnnotation();
        $subQuery = sprintf('(SELECT %s FROM %s WHERE %s IN (\'%s\'))',
            $categoryProductParameterAnnotation->getPropertyByName('categoryId')->getColumn()->getName(),
            $categoryProductParameterAnnotation->getTable()->getName(),
            $categoryProductParameterAnnotation->getPropertyByName('productParameterId')->getColumn()->getName(),
            implode('\',\'', $parameterId));
        $filter['where'][] = ['categoryId', 'IN.SQL', $subQuery];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $categoryId int
     * @param $parameterId int
     * @return CategoryProductParameterEntity
     */
    public function findOneByCategoryIdAndParameterId(int $categoryId, int $parameterId)
    {
        $filters['where'][] = ['categoryId', '=', $categoryId];
        $filters['where'][] = ['productParameterId', '=', $parameterId];
        return $this->findOneBy($filters) ?: NULL;
    }



    /**
     * Find joined.
     * @param $filters array
     * @return array
     */
    public function findJoined(array $filters) : array
    {
        $filters['join'] = $this->getParameterJoins();
        $mapping = function ($results) {
            return $results;
        };

        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filters, $mapping) ?: [];
    }



    /**
     * @param $filters array
     * @return CountDTO
     */
    public function countJoined(array $filters) : CountDTO
    {
        $filters['join'] = $this->getParameterJoins();
        return $this->count($filters);
    }



    /**
     * todo názvy nahradit daty z entity
     * @return array
     */
    protected function getParameterJoins() : array
    {
        return [
            ['LEFT JOIN', 'product_parameter', 'pp_id = cpr_product_parameter_id'],
            ['LEFT JOIN', 'product_parameter_translation', 'ppt_product_parameter_id = pp_id'],
            ['LEFT JOIN', 'product_parameter_group', 'ppg_id = pp_product_parameter_group_id'],
            ['LEFT JOIN', 'product_parameter_group_translation', 'ppgt_product_parameter_group_id = ppg_id'],
        ];
    }
}
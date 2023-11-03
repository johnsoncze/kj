<?php

declare(strict_types = 1);

namespace App\Diamond\Price;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PriceRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Price::class;



    /**
     * @param $diamondId array
     * @return int[]|array
     */
    public function findQualityIdByMoreDiamondId(array $diamondId) : array
    {
        //todo load property from DISTINCT(..)
        $filter['columns'] = ['DISTINCT(dp_quality_id) AS qualityId'];
        $filter['where'][] = ['diamondId', '', $diamondId];
        return $this->getEntityMapper()
            ->getQueryManager(Price::class)
            ->findBy($filter, function ($rows) {
                $id = [];
                foreach ($rows as $row) {
                    $id[] = $row['qualityId'];
                }
                return $id;
            });
    }



    /**
     * @param $filter array
     * @return Price[]|array
     */
    public function findJoined(array $filter) : array
    {
        $filter['join'] = $this->getJoinCondition();

        $mapping = function ($results) {
            return $results;
        };
        return $this->getEntityMapper()
            ->getQueryManager($this->getEntityName())
            ->findBy($filter, $mapping) ?: [];
    }



    /**
     * @param $filter array
     * @return CountDTO
     */
    public function countJoined(array $filter) : CountDTO
    {
        $filter['join'] = $this->getJoinCondition();
        return $this->count($filter);
    }



    /**
     * @return array
     */
    private function getJoinCondition() : array
    {
        return [
            ['LEFT JOIN', 'diamond', 'd_id = dp_diamond_id'],
            ['LEFT JOIN', 'product_parameter', 'pp_id = dp_quality_id'],
            ['LEFT JOIN', 'product_parameter_translation', 'ppt_product_parameter_id = pp_id'],
        ];
    }
}
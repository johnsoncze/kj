<?php

declare(strict_types = 1);

namespace App\Product\Diamond;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DiamondRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Diamond::class;



    /**
     * @param $id int
     * @return Diamond
     * @throws NotFoundException
     */
    public function getOneById(int $id) : Diamond
    {
        $filter['where'][] = ['id', '=', $id];
        $diamond = $this->findOneBy($filter);
        if (!$diamond) {
            throw new NotFoundException('Diamond not found.');
        }
        return $diamond;
    }



    /**
     * @param $productId int
     * @param $diamondId int
     * @param $gender string|null
     * @return Diamond|null
     */
    public function findOneByProductIdAndDiamondIdAndGender(int $productId, int $diamondId, string $gender = NULL)
    {
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['diamondId', '=', $diamondId];
        if ($gender !== NULL) {
            $filter['where'][] = ['gender', '=', $gender];
        }
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $productId int
     * @return Diamond[]|array
     */
    public function findByProductId(int $productId) : array
    {
        $filter['where'][] = ['productId', '=', $productId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $productId int
     * @param $gender string
     * @return Diamond[]|array
    */
    public function findByProductIdAndGender(int $productId, string $gender) : array
    {
        $filter['where'][] = ['productId', '=', $productId];
        $filter['where'][] = ['gender', '=', $gender];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $productId int
     * @return CountDTO
     */
    public function countByProductId(int $productId) : CountDTO
    {
        $filter['where'][] = ['productId', '=', $productId];
        return $this->count($filter);
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
            ['LEFT JOIN', 'diamond', 'd_id = pd_diamond_id'],
        ];
    }
}
<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock\Parameter;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ParameterRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = Parameter::class;



    /**
     * @param $lockId array
     * @return Parameter[]|array
     */
    public function findByMoreLockId(array $lockId) : array
    {
		$filter['sort'] = [['LENGTH(weight)', 'weight'], 'ASC'];
        $filter['where'][] = ['lockId', '', $lockId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $lockId array
     * @param $parameterId int
     * @return Parameter[]|array
     */
    public function findByMoreLockIdAndParameterId(array $lockId, int $parameterId) : array
    {
        $filter['where'][] = ['lockId', '', $lockId];
        $filter['where'][] = ['parameterId', '=', $parameterId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $lockId int
     * @param $parameterId int
     * @return Parameter|null
     */
    public function findOneByLockIdAndParameterId(int $lockId, int $parameterId)
    {
        $filter['where'][] = ['lockId', '=', $lockId];
        $filter['where'][] = ['parameterId', '=', $parameterId];
        return $this->findOneBy($filter) ?: NULL;
    }



    /**
     * @param $lockId int
     * @param $parameterId int[]
     * @return Parameter[]|array
     */
    public function findByLockIdAndMoreParameterId(int $lockId, array $parameterId) : array
    {
        $filter['where'][] = ['lockId', '=', $lockId];
        $filter['where'][] = ['parameterId', '', $parameterId];
        return $this->findBy($filter) ?: [];
    }
}
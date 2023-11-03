<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LockRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = Lock::class;



    /**
     * @param $key string
     * @return Lock[]|array
     * @throws NotFoundException
     */
    public function getByKey(string $key) : array
    {
        $filter['where'][] = ['key', '=', $key];
        $result = $this->findBy($filter);
        if (!$result) {
            throw new NotFoundException(sprintf('Not found lock with key \'%s\'.', $key));
        }
        return $result;
    }



    /**
     * @param $key string
     * @return Lock
     * @throws NotFoundException
     */
    public function getOneByKey(string $key) : Lock
    {
        $locks = $this->getByKey($key);
        return end($locks);
    }



    /**
     * @param $groupId int
     * @return Lock[]|array
     */
    public function findByGroupId(int $groupId) : array
    {
        $filter['where'][] = ['groupId', '=', $groupId];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @param $keys array
     * @return Lock[]|array
     */
    public function findByMoreKeys(array $keys) : array
    {
        $filter['where'][] = ['key', '', $keys];
        return $this->findBy($filter);
    }
}
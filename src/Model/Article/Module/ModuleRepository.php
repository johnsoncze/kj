<?php

declare(strict_types = 1);

namespace App\Article\Module;

use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ModuleRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = Module::class;



    /**
     * @param $id int
     * @return Module
     * @throws NotFoundException
     */
    public function getOneById(int $id): Module
    {
        $filter['where'][] = ['id', '=', $id];
        $module = $this->findOneBy($filter);
        if (!$module) {
            throw new NotFoundException('Module not found.');
        }
        return $module;
    }



    /**
     * @param $id array
     * @return Module[]|array
     */
    public function findByMoreId(array $id): array
    {
        $filter['where'][] = ['id', '', $id];
        return $this->findBy($filter) ?: [];
    }
}
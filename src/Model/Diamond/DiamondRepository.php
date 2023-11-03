<?php

declare(strict_types = 1);

namespace App\Diamond;

use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DiamondRepository extends BaseRepository
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
     * @return Diamond[]|array
     */
    public function findAll()
    {
        $filter['sort'] = ['size'];
        return $this->findBy($filter) ?: [];
    }
}
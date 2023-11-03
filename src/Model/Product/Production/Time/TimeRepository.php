<?php

declare(strict_types = 1);

namespace App\Product\Production\Time;

use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TimeRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = Time::class;



    /**
     * @param $id int
     * @return Time
     * @throws NotFoundException
     */
    public function getOnePublishedById(int $id) : Time
    {
        $filter['where'][] = ['id', '=', $id];
        $filter['where'][] = ['state', '=', Time::PUBLISH];
        $time = $this->findOneBy($filter);
        if ($time === NULL) {
            throw new NotFoundException('Production time not found.');
        }
        return $time;
    }



    /**
     * @return Time[]|array
     */
    public function findPublished() : array
    {
        $filter['where'][] = ['state', '=', Time::PUBLISH];
        $filter['sort'] = [['LENGTH(sort)', 'sort'], 'ASC'];
        return $this->findBy($filter) ?: [];
    }



    /**
     * @return Time|null
     */
    public function findDefaultPublished()
    {
        $filter['where'][] = ['default', '=', TRUE];
        $filter['where'][] = ['state', '=', Time::PUBLISH];
        return $this->findOneBy($filter) ?: NULL;
    }
}
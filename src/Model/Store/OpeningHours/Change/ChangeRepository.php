<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours\Change;

use App\Extensions\Grido\IRepositorySource;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ChangeRepository extends BaseRepository implements IRepositorySource
{


    /** @var string */
    protected $entityName = Change::class;



    /**
     * @param $date \DateTime
     * @return Change|null
     */
    public function findOneByDate(\DateTime $date)
    {
        $filter['where'][] = ['date', '=', $date->format('Y-m-d')];
        return $this->findOneBy($filter) ?: NULL;
    }
}
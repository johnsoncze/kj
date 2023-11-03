<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpeningHoursRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = OpeningHours::class;



    /**
     * @param $day string
     * @return OpeningHours|null
     */
    public function findOneByDay(string $day)
    {
        $filter['where'][] = ['day', '=', $day];
        return $this->findOneBy($filter) ?: NULL;
    }
}
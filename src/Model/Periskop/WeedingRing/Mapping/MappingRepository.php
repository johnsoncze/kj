<?php

declare(strict_types = 1);

namespace App\Periskop\WeedingRing\Mapping;

use App\IRepository;
use App\NotFoundException;
use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MappingRepository extends BaseRepository implements IRepository
{


    /** @var string */
    protected $entityName = Mapping::class;



    /**
     * @param $maleId int
     * @param $femaleId int
     * @return Mapping
     * @throws NotFoundException
     */
    public function getOneByMaleIdAndFemaleId(int $maleId, int $femaleId) : Mapping
    {
        $filter['where'][] = ['maleId', '=', $maleId];
        $filter['where'][] = ['femaleId', '=', $femaleId];
        $mapping = $this->findOneBy($filter);
        if (!$mapping) {
            throw new NotFoundException('Mapping not found.');
        }
        return $mapping;
    }
}
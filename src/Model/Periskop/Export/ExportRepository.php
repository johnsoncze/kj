<?php

declare(strict_types = 1);

namespace App\Periskop\Export;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ExportRepository extends BaseRepository
{


    /** @var string */
    protected $entityName = Export::class;



    /**
     * @param $type string
     * @return Export|null
     */
    public function findOneLastByType(string $type)
    {
        $filter['limit'] = 1;
        $filter['sort'] = ['addDate', 'DESC'];
        $filter['where'][] = ['type', '=', $type];
        return $this->findOneBy($filter) ?: NULL;
    }
}
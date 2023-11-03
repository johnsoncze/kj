<?php

namespace App\Extensions\Grido;

use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IRepositorySource
{


    /**
     * @param $filters array
     * @return null|IEntity[]
     */
    public function findBy(array $filters);



    /**
     * @param $filters array
     * @return CountDTO
     */
    public function count($filters);
}
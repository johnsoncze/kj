<?php

namespace App\Language;

use App\Helpers\Entities;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageListService extends NObject
{


    /**
     * @param $entities LanguageEntity[]
     * @return array
     */
    public function getList(array $entities)
    {
        return Entities::toPair($entities, "id", "name");
    }
}
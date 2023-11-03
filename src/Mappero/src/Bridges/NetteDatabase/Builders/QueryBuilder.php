<?php

namespace Ricaefeliz\Mappero\Bridges\NetteDatabase\Builders;

use Ricaefeliz\Mappero\Annotations\Column;
use Ricaefeliz\Mappero\Exceptions\QueryBuilderException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class QueryBuilder extends NObject
{


    /**
     * @param $columns array
     * @return string
     * @throws QueryBuilderException
     */
    public function getColumnsQuery(array $columns)
    {
        $query = "";
        foreach ($columns as $column) {
            $query .= ($column instanceof Column ? $column->getName() : $column) . ",";
        }
        $query = trim($query, ",");
        return $query;
    }


}
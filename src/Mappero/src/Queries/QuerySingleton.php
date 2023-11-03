<?php

namespace Ricaefeliz\Mappero\Queries;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class QuerySingleton extends NObject
{


    /** @var IQuery[]|array */
    protected static $queries = [];



    /**
     * @param $name string
     * @return object|null
     */
    public static function getQuery($name)
    {
        if (isset(self::$queries[$name])) {
            return self::$queries[$name];
        }
        return NULL;
    }



    /**
     * @param $query IQuery
     * @return IQuery
     */
    public static function saveQuery(IQuery $query)
    {
        self::$queries[$query->getName()];
        return $query;
    }
}
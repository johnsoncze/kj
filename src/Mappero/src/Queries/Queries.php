<?php

namespace Ricaefeliz\Mappero\Queries;

use App\NObject;
use Ricaefeliz\Mappero\Bridges\NetteDatabase\NetteDatabase;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Queries extends NObject
{


    /** @var NetteDatabase */
    protected $database;

    /** @var array|IQuery[] */
    protected $queries = [];



    public function __construct(NetteDatabase $database)
    {
        $this->database = $database;
    }



    /**
     * @param $name string
     * @return IQuery
     */
    public function getQuery($name)
    {
        if (!isset($this->queries[$name])) {
            $query = new $name($this->database);
            $this->queries[$name] = $query;
        }
        return $this->queries[$name];
    }
}
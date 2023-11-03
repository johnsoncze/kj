<?php

namespace Ricaefeliz\Mappero\Queries;

use Ricaefeliz\Mappero\Bridges\NetteDatabase\NetteDatabase;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseQuery extends NObject
{


    /** @var NetteDatabase */
    protected $database;



    public function __construct(NetteDatabase $database)
    {
        $this->database = $database;
    }



    /**
     * @return string
     */
    public function getName()
    {
        return get_called_class();
    }
}
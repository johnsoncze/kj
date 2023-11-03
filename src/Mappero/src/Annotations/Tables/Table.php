<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Ricaefeliz\Mappero\Exceptions\TableException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Table extends NObject
{


    /** @var string name of annotation */
    const TABLE = "Table";

    /** @var string */
    const DEFAULT_TYPE = "default";
    const VIEW_TYPE = "view";

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;



    public function __construct(string $name, string $type)
    {
        //check type of table
        if (!in_array($type, [self::DEFAULT_TYPE, self::VIEW_TYPE], TRUE)) {
            throw new TableException(sprintf("Unknown type '%s' of table.", $type));
        }

        $this->name = $name;
        $this->type = $type;
    }



    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }



    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
}
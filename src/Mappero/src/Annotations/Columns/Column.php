<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Column extends NObject
{


    /** @var string name of annotation */
    const COLUMN = "Column";

    /** @var string name of primary key */
    const PRIMARY_KEY = "Primary";

    /** @var string */
    const TYPE_TIMESTAMP = 'timestamp';

    /** @var bool */
    protected $primary = FALSE;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $type;

    /** @var array */
    protected static $types = [
        self::TYPE_TIMESTAMP,
    ];



    public function __construct(string $name, string $type = NULL)
    {
        $this->name = $name;
        $type && $this->type = $this->checkType($type);
    }



    /**
     * @param bool $arg
     * @return Column
     */
    public function setPrimary(bool $arg) : self
    {
        $this->primary = $arg;
        return $this;
    }



    /**
     * @return bool
     */
    public function isPrimary() : bool
    {
        return $this->primary;
    }



    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }



    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }



    /**
     * @param $type string
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function checkType(string $type) : string
    {
        if (!in_array($type, self::$types, TRUE)) {
            throw new \InvalidArgumentException(sprintf('Unknown column type \'%s\'.', $type));
        }
        return $type;
    }
}
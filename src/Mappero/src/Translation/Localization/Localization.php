<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Translation;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Localization extends NObject
{


    /** @var int|string */
    protected $id;

    /** @var string */
    protected $prefix;



    public function __construct($id, string $prefix)
    {
        $this->id = $id;
        $this->prefix = $prefix;
    }



    public function getId()
    {
        return $this->id;
    }



    /**
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }
}
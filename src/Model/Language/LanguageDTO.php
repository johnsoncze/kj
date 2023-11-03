<?php

declare(strict_types = 1);

namespace App\Language;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class LanguageDTO
{


    /** @var int|string */
    protected $id;

    /** @var string */
    protected $prefix;



    public function __construct(int $id, string $prefix)
    {
        $this->id = $id;
        $this->prefix = $prefix;
    }



    /**
     * @return int
     */
    public function getId() : int
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
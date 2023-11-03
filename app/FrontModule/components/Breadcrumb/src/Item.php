<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Breadcrumb;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item
{


    /** @var string */
    protected $name;

    /** @var string|null */
    protected $link;



    public function __construct(string $name, string $link = NULL)
    {
        $this->name = $name;
        $this->link = $link;
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
    public function getLink()
    {
        return $this->link;
    }


}
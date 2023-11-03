<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Item
{


    /** @var string */
    protected $title;

    /** @var string */
    protected $name;

    /** @var bool */
    protected $isChecked;



    public function __construct(string $title, string $name, bool $isChecked = FALSE)
    {
        $this->title = $title;
        $this->name = $name;
        $this->isChecked = $isChecked;
    }



    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }



    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }



    /**
     * @return boolean
     */
    public function isChecked() : bool
    {
        return $this->isChecked;
    }


}
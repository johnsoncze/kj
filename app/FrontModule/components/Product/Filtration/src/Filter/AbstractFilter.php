<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class AbstractFilter
{


    /** @var string */
    protected $name;

    /** @var string */
    protected $title;

    /** @var string|null */
    protected $tooltip;



    public function __construct(string $title, string $name)
    {
        $this->title = $title;
        $this->name = $name;
    }



    /**
     * @param $text string
     * @return self
     */
    public function setTooltip(string $text = NULL) : self
    {
        $this->tooltip = $text;
        return $this;
    }



    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }



    /**
     * @return null|string
     */
    public function getTooltip()
    {
        return $this->tooltip;
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
    abstract public function getType() : string;
}
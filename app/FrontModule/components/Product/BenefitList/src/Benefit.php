<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\BenefitList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Benefit
{


    /** @var string */
    protected $icon;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;



    public function __construct(string $icon, string $title, string $description)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }



    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }



    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }



    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


}
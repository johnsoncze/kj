<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IFilter
{


    /**
     * @return string
     */
    public function getTitle();



    /**
     * @return string|null
     */
    public function getTooltip();



    /**
     * @return string
     */
    public function getType();



    /**
     * Is the filter being filtered?
     * @return bool
     */
    public function isFiltered() : bool;



    /**
     * @return string
     */
    public function getName() : string;
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\Filtration\Filter;

use App\FrontModule\Components\Category\Filtration\Filter\IFilter;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FilterCollection
{


    /** @var IFilter|array */
    protected $filters = [];



    /**
     * @param $filter IFilter
     * @return self
     * @throws \InvalidArgumentException duplicit filter name
     */
    public function add(IFilter $filter) : self
    {
        $name = $filter->getName();
        if (isset($this->filters[$name])) {
            throw new \InvalidArgumentException(sprintf('Duplicate filter name \'%s\'.', $name));
        }
        $this->filters[$name] = $filter;
        return $this;
    }



    /**
     * @param $name string
     * @return IFilter|null
     */
    public function getByName(string $name)
    {
        return $this->filters[$name] ?? NULL;
    }



    /**
     * @param $name string
     * @return void
     */
    public function removeByName(string $name)
    {
        unset($this->filters[$name]);
    }



    /**
     * @return IFilter[]|array
     */
    public function getFilters() : array
    {
        return $this->filters;
    }
}
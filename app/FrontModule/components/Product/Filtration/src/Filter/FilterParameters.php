<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class FilterParameters extends AbstractFilter implements IFilter
{


    /** @var Parameter[]|array */
    protected $parameters = [];

    /** @var bool */
    protected $hasCheckedItem = FALSE;



    /**
     * @param $parameter Parameter
     * @return self
     */
    public function addParameter(Parameter $parameter) : self
    {
        if ($parameter->isChecked() === TRUE) {
            $this->hasCheckedItem = TRUE;
        }
        $this->parameters[] = $parameter;
        return $this;
    }



    /**
     * @return Parameter[]|array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }



    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return 'filter_parameters';
    }



    /**
     * @inheritdoc
     */
    public function isFiltered() : bool
    {
        return $this->hasCheckedItem;
    }


}
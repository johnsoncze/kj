<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CheckboxFilter extends AbstractFilter implements IFilter
{


    /** @var bool */
    protected $isChecked = FALSE;



    /**
     * @param $arg bool
     * @return self
     */
    public function setIsChecked(bool $arg) : self
    {
        $this->isChecked = $arg;
        return $this;
    }



    /**
     * @return bool
     */
    public function isChecked() : bool
    {
        return $this->isChecked;
    }



    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return 'checkbox_filter';
    }



    /**
     * @inheritdoc
     */
    public function isFiltered() : bool
    {
        return $this->isChecked();
    }


}
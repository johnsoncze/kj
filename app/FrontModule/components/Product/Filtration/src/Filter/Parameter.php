<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Filtration\Filter;

use App\ProductParameter\ProductParameterEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Parameter
{


    /** @var ProductParameterEntity */
    protected $parameter;

    /** @var bool */
    protected $isChecked = FALSE;

    /** @var bool */
    protected $isDisabled = FALSE;

    /** @var int */
    protected $productCount = 0;



    public function __construct(ProductParameterEntity $parameter)
    {
        $this->parameter = $parameter;
    }



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
     * @param $arg bool
     * @return self
    */
    public function setIsDisabled(bool $arg) : self
    {
        $this->isDisabled = $arg;
        return $this;
    }



    /**
     * @param $count int
     * @return self
     */
    public function setProductCount(int $count) : self
    {
        $this->productCount = $count;
        return $this;
    }



    /**
     * @return ProductParameterEntity
     */
    public function getParameter() : ProductParameterEntity
    {
        return $this->parameter;
    }



    /**
     * @return boolean
     */
    public function isChecked() : bool
    {
        return $this->isChecked;
    }



    /**
     * @return bool
    */
    public function isDisabled() : bool
    {
        return $this->isDisabled;
    }



    /**
     * @return int
     */
    public function getProductCount() : int
    {
        return $this->productCount;
    }


}
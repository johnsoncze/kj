<?php

declare(strict_types = 1);

namespace App\Remarketing\Code;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CodeDTO
{


    /** @var string data keys */
    const DATA_PAGE_TYPE_KEY = 'ecomm_pagetype';
    const DATA_PRODID_KEY = 'ecomm_prodid';
    const DATA_CATEGORY = 'ecomm_category';
    const DATA_TOTALVALUE_KEY = 'ecomm_totalvalue';

    /** @var string page types */
    const PAGE_TYPE_CART = 'cart';
    const PAGE_TYPE_CATEGORY = 'category';
    const PAGE_TYPE_HOME = 'home';
    const PAGE_TYPE_OTHER = 'other';
    const PAGE_TYPE_PRODUCT = 'product';
    const PAGE_TYPE_PURCHASE = 'purchase';
    const PAGE_TYPE_SEARCH_RESULTS = 'searchresults';

    /** @var array */
    protected $data = [];



    public function __construct(string $pageType = self::PAGE_TYPE_OTHER)
    {
        $this->setPageType($pageType);
    }



    /**
     * @param $type string
     * @return self
     */
    public function setPageType(string $type) : self
    {
        $this->data[self::DATA_PAGE_TYPE_KEY] = $type;
        return $this;
    }



    /**
     * @param $data array
     * @return self
     */
    public function setData(array $data) : self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }



    /**
     * @return bool
     */
    public function hasData() : bool
    {
        return count($this->data) > 0;
    }



    /**
     * @return string
     */
    public function getDataInJson()
    {
        $data = $this->data;
        return $data ? json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : [];
    }
}
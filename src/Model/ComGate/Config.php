<?php

declare(strict_types = 1);

namespace App\ComGate;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Config
{


    /** @var string */
    protected $url;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $storeId;

    /** @var bool */
    protected $test;



    public function __construct(string $url, string $storeId, string $secret, bool $test)
    {
        $this->url = $url;
        $this->secret = $secret;
        $this->storeId = $storeId;
        $this->test = $test;
    }



    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }



    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }



    /**
     * @return string
     */
    public function getStoreId(): string
    {
        return $this->storeId;
    }



    /**
     * @return bool
     */
    public function isTest() : bool
    {
        return $this->test;
    }
}
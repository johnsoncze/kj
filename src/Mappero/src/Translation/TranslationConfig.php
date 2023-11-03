<?php

namespace Ricaefeliz\Mappero\Translation;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationConfig extends NObject
{


    /** @var array */
    protected $whiteList;

    /** @var string */
    protected $default;

    /** @var string */
    protected $actual;



    public function __construct(array $whiteList, string $default)
    {
        $this->whiteList = $whiteList;
        $this->default = $default;
    }



    /**
     * @param string $lang
     * @return TranslationConfig
     */
    public function setActual(string $lang) : self
    {
        $this->actual = $lang;
        return $this;
    }



    /**
     * @return array
     */
    public function getWhiteList() : array
    {
        return $this->whiteList;
    }



    /**
     * @return string
     */
    public function getActual() : string
    {
        return 'cs';
        return $this->actual ?: $this->default;
    }



    /**
     * @return string
     */
    public function getDefault() : string
    {
        return $this->default;
    }

}

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationConfigException extends \Exception
{


}
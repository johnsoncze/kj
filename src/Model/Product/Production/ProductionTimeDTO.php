<?php

declare(strict_types = 1);

namespace App\Product\Production;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductionTimeDTO
{


    /** @var string production times */
    const PRODUCTION_24_HOURS = '24_hours';
    const PRODUCTION_4_6_WEEKS = '4_6_weeks';


    /** @var string */
    protected $key;

    /** @var string */
    protected $translationKey;

    /** @var float */
    protected $surchargePercent;



    public function __construct(string $key, string $translationKey, float $surchargePercent = 0.0)
    {
        $this->key = $key;
        $this->translationKey = $translationKey;
        $this->surchargePercent = $surchargePercent;
    }



    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }



    /**
     * @return string
     */
    public function getTranslationKey() : string
    {
        return $this->translationKey;
    }



    /**
     * @return float
     */
    public function getSurchargePercent() : float
    {
        return $this->surchargePercent;
    }



    /**
     * @return bool
     */
    public function hasSurcharge() : bool
    {
        return $this->surchargePercent !== 0.0;
    }
}
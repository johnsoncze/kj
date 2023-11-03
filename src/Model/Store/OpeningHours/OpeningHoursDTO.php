<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours;

use App\Store\OpeningHours\Change\Change;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpeningHoursDTO
{


    /** @var string|null */
    protected $openingTime;

    /** @var string|null */
    protected $closingTime;



    public function __construct(string $openingTime = NULL,
                                string $closingTime = NULL)
    {
        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
    }



    /**
     * @return string|null
     */
    public function getOpeningTime()
    {
        return $this->openingTime;
    }



    /**
     * @return string|null
     */
    public function getClosingTime()
    {
        return $this->closingTime;
    }



    /**
     * @param $change Change
     * @return self
     */
    public static function createFromChange(Change $change) : self
    {
        return new self($change->getOpeningTime(), $change->getClosingTime());
    }



    /**
     * @param $openingHours OpeningHours
     * @return self
     */
    public static function createFromOpeningHours(OpeningHours $openingHours) : self
    {
        return new self($openingHours->getOpeningTime(), $openingHours->getClosingTime());
    }
}
<?php

declare(strict_types = 1);

namespace App\Helpers;

use Nette\StaticClass;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Prices
{


    use StaticClass;



    /**
     * Convert price in float format to string.
     * @param $price float
     * @return string
     */
    public static function toString(float $price) : string
    {
        return number_format($price, 4, '.', '');
    }



    /**
     * Check if price is not zero or less than 0.00.
     * @param $price float
     * @return bool
     */
    public static function isValid(float $price) : bool
    {
        return $price > 0.00;
    }



    /**
     * Subtract percent.
     * @param $price float
     * @param $percent float
     * @return float
     * @throws \InvalidArgumentException more than 100 percent
     */
    public static function subtractPercent(float $price, float $percent) : float
    {
        if ($percent > 100) {
            throw new \InvalidArgumentException('Percent can not be more than 100.');
        }
        if ($percent !== 0.0) {
            $price = $price * (100 - $percent) / 100;
        }
        return self::format((float)$price);
    }



    /**
     * Add percent.
     * @param $price float
     * @param $percent float
     * @return float
     * @throws \InvalidArgumentException negative percent
     */
    public static function addPercent(float $price, float $percent) : float
    {
        if ($percent < 0) {
            throw new \InvalidArgumentException('Percent can not be less than zero.');
        }
        if ($percent !== 0.0) {
            $part = $price * $percent / 100;
            $price += $part;
        }
        return self::format((float)$price);
    }



    /**
     * @param $price float
     * @param $discount float
     * @return float
     * todo make more general name
     */
    public static function toBeforeDiscount(float $price, float $discount) : float
    {
        $basePercent = 100 - $discount;
        $result = $price * 100 / $basePercent;
        return self::format((float)$result);
    }



    /**
     * @param $price
     * @param $percent float
     * @return float
     */
    public static function toBeforePercent(float $price, float $percent) : float
    {
        $result = $price / (float)('1.' . str_replace(['.', ','], '', (string)$percent));
        return self::format((float)$result);
    }



    /**
     * Format price to accountant format.
     * @param $price float
     * @return float
     */
    public static function format(float $price) : float
    {
        return (float)number_format($price, 4, '.', '');
    }



    /**
     * @param $price float
     * @return string
    */
    public static function toUserFriendlyFormat(float $price) : string
    {
        return number_format($price, 0, '', ' ');
    }
}
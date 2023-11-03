<?php

namespace App\Helpers;

class Strings extends \Nette\Utils\Arrays
{

    /**
     * Return string with 4bytes utf chars removed.
     */
    public static function mb4tomb3(string $string): string
    {
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $string);
    }
}
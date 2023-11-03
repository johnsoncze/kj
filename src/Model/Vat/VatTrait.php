<?php

declare(strict_types = 1);

namespace App\Vat;

use App\Helpers\Prices;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait VatTrait
{


    /**
     * Get vat list.
     * @return array
     */
    public static function getVatList() : array
    {
        return [
            Prices::toString(21.0) => '21 %',
        ];
    }



    /**
     * Check if vat is valid.
     * @param $vat float
     * @return bool
     */
    public function isVatValid(float $vat) : bool
    {
        $vatList = self::getVatList();
        $price = Prices::toString($vat);
        return isset($vatList[$price]);
    }
}
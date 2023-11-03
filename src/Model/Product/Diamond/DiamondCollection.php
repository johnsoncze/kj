<?php

declare(strict_types = 1);

namespace App\Product\Diamond;

use App\Diamond\Price\Price;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DiamondCollection
{


    /** @var Diamond[] */
    protected $diamonds;



    public function __construct(array $diamonds)
    {
        if (!$diamonds) {
            throw new \InvalidArgumentException('Missing diamonds.');
        }
        foreach ($diamonds as $diamond) {
            if (!$diamond instanceof Diamond) {
                throw new \InvalidArgumentException(sprintf('Object must be instance of \'%s\'.', Diamond::class));
            }
            $this->diamonds[] = $diamond;
        }
    }



    /**
     * @return Diamond[]
     */
    public function getDiamonds() : array
    {
        return $this->diamonds;
    }



    /**
     * @return array
     */
    public function getPriceQualityId() : array
    {
        $id = [];
        $diamonds = $this->getDiamonds();
        foreach ($diamonds as $diamond) {
            $diamondPrices = $diamond->getDiamond()->getPrices();
            /** @var $diamondPrices Price[] */
            foreach ($diamondPrices as $diamondPrice) {
                $id[] = $diamondPrice->getQualityId();
            }
        }
        return $id;
    }
}
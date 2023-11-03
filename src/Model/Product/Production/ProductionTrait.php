<?php

declare(strict_types = 1);

namespace App\Product\Production;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * todo přesunout seznam production time do databáze!!
 * ČÍSELNÍK JE JIŽ ČÁSTEČNĚ V DATABÁZI A JE TŘEBA UPRAVOVAT HODNOTY I TAM.
 */
trait ProductionTrait
{


    /**
     * Check if is production time valid.
     * @param $time string
     * @return string
     * @throws \InvalidArgumentException invalid time
     */
    protected function checkProductionTime(string $time) : string
    {
        if (array_key_exists($time, self::getProductionTimes()) !== TRUE) {
            throw new \InvalidArgumentException(sprintf('Unknown \'%s\' production time.', $time));
        }
        return $time;
    }



    /**
     * @return ProductionTimeDTO[]
     */
    public static function getProductionTimes() : array
    {
        static $times = [];
        if (!$times) {
            $times[ProductionTimeDTO::PRODUCTION_4_6_WEEKS] = new ProductionTimeDTO(ProductionTimeDTO::PRODUCTION_4_6_WEEKS, 'product.production.' . ProductionTimeDTO::PRODUCTION_4_6_WEEKS);
        }
        return $times;
    }



    /**
     * @param $object bool
     * @return ProductionTimeDTO|string|null
    */
    public function getProductionTime(bool $object = FALSE)
    {
        $time = $this->productionTime;
        return $object === TRUE ? self::getProductionTimes()[$time] : $time;
    }
}
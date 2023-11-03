<?php

declare(strict_types = 1);

namespace App\Tests\Helpers;

require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\Prices;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PricesTest extends BaseTestCase
{


    /**
     * @dataProvider getSubtractPercentLoop
     * @param $price float
     * @param $percent float
     * @param $result float
     */
    public function testSubtractPercent(float $price, float $percent, float $result)
    {
        Assert::same($result, Prices::subtractPercent($price, $percent));
    }



    /**
     * @dataProvider getAddPercentLoop
     * @param $price float
     * @param $percent float
     * @param $result float
    */
    public function testAddPercent(float $price, float $percent, float $result)
    {
        Assert::same($result, Prices::addPercent($price, $percent));
    }



    /**
     * @return array
     */
    public function getSubtractPercentLoop() : array
    {
        $loop[] = [120.0, 55.0, 54.0];
        $loop[] = [578.0, 61, 225.420];
        $loop[] = [784.7, 3, 761.159];
        $loop[] = [150.45, 0.0, 150.45];
        return $loop;
    }



    /**
     * @return array
    */
    public function getAddPercentLoop() : array
    {
        $loop[] = [120.0, 55.0, 186.0];
        $loop[] = [578.0, 61, 930.580];
        $loop[] = [784.7, 3, 808.241];
        $loop[] = [150.45, 0.0, 150.45];
        return $loop;
    }
}

(new PricesTest())->run();
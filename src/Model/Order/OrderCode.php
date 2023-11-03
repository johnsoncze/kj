<?php

declare(strict_types = 1);

namespace App\Order;

use Nette\Utils\DateTime;
use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OrderCode
{


    /**
     * Generate new code.
     * @return string
     */
    public function generate() : string
    {
        $actualDate = new DateTime();
        $number = Random::generate(7, '0-9');

        //max 10 length because variable number of transfer payment and more..
        return 'O' . $actualDate->format('y') . $number;
    }
}
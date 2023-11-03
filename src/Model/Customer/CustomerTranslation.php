<?php

declare(strict_types = 1);

namespace App\Customer;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CustomerTranslation
{


    /**
     * @return string
     */
    public static function getFileKey() : string
    {
        return 'customer';
    }
}
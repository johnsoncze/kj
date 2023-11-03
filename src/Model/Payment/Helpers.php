<?php

declare(strict_types = 1);

namespace App\Payment;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Helpers
{


    /**
     * @return string
     */
    public static function getTranslationFileKey() : string
    {
        return 'payment';
    }
}
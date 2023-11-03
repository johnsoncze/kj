<?php

declare(strict_types = 1);

namespace App\Helpers;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Images extends NObject
{


    /**
     * @return string[]
     */
    public static function getMimeTypes() : array
    {
        return ['image/gif', 'image/png', 'image/jpeg'];
    }
}
<?php

declare(strict_types = 1);

namespace App;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ExternalSystemIdTrait
{


    /**
     * Check if external system id is valid.
     * @param $id int
     * @return bool
     */
    protected function isValidExternalSystemId(int $id) : bool
    {
        return $id > 0;
    }
}
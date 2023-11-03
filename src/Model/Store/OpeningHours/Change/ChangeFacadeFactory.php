<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours\Change;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ChangeFacadeFactory
{


    /**
     * @return ChangeFacade
     */
    public function create();
}
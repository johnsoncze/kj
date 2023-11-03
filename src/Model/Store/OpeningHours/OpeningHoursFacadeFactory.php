<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpeningHoursFacadeFactory
{


    /**
     * @return OpeningHoursFacade
     */
    public function create();
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\OpeningHours;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface OpeningHoursFactory
{


    /**
     * @return OpeningHours
     */
    public function create();
}
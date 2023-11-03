<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Store\OpeningHours\ChangeList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ChangeListFactory
{


    /**
     * @return ChangeList
     */
    public function create();
}
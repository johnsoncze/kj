<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\HomepageList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface HomepageListFactory
{


    /**
     * @return HomepageList
     */
    public function create();
}
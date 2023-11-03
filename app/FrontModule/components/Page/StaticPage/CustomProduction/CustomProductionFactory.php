<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\CustomProduction;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomProductionFactory
{


    /** @return CustomProduction */
    public function create();
}
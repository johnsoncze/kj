<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\GoldsmithWorkshop;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface GoldsmithWorkshopFactory
{


    /**
     * @return GoldsmithWorkshop
     */
    public function create();
}
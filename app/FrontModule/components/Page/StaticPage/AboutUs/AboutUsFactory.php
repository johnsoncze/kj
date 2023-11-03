<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\AboutUs;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface AboutUsFactory
{


    /**
     * @return AboutUs
     */
    public function create();
}
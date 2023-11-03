<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Services;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ServicesFactory
{


    /**
     * @return Services
     */
    public function create();
}
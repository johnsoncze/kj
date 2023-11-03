<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Team;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface TeamFactory
{


    /**
     * @return Team
     */
    public function create();
}
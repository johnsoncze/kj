<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Showroom;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShowroomFactory
{


    /**
     * @return Showroom
     */
    public function create();
}
<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\Timeline;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface TimelineFactory
{


    /**
     * @return Timeline
     */
    public function create();
}
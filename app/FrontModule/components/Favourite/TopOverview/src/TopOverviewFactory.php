<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Favourite\TopOverview;


interface TopOverviewFactory
{


    /**
     * @return TopOverview
     */
    public function create();
}
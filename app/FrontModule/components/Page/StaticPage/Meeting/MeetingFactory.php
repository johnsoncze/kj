<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\StaticPage\Meeting;


interface MeetingFactory
{


    /**
     * @return Meeting
     */
    public function create();
}
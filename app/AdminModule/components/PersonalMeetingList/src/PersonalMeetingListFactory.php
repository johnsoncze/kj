<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PersonalMeetingList;



interface PersonalMeetingListFactory
{


    /**
     * @return PersonalMeetingList
     */
    public function create();
}
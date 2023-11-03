<?php

declare(strict_types = 1);

namespace App\AdminModule\Presenters;

use App\PersonalMeeting\PersonalMeeting;
use App\PersonalMeeting\PersonalMeetingRepository;
use App\AdminModule\Components\PersonalMeetingList\PersonalMeetingList;
use App\AdminModule\Components\PersonalMeetingList\PersonalMeetingListFactory;




final class PersonalMeetingPresenter extends AdminModulePresenter
{
    /** @var PersonalMeeting|null */
    public $personalMeeting;

    /** @var PersonalMeetingListFactory @inject */
    public $personalMeetingListFactory;

    /** @var PersonalMeetingRepository @inject */
    public $personalMeetingRepo;


    /**
     * Action 'detail'.
     * @param $id int
     * @return void
     */
    public function actionDetail(int $id)
    {
        $this->personalMeeting = $personalMeeting = $this->checkRequest($id, PersonalMeetingRepository::class);

        $this->template->personalMeeting = $personalMeeting;
    }


    /**
     * @return PersonalMeetingList
     */
    public function createComponentPersonalMeetingList() : PersonalMeetingList
    {
        $list = $this->personalMeetingListFactory->create();
        return $list;
    }
		
	
    public function renderDetail()
    {
        $this->template->setFile(__DIR__ . '/templates/PersonalMeeting/detail.latte');
    }

}
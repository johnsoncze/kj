<?php

declare(strict_types = 1);

namespace App\PersonalMeeting;

use App\Extensions\Grido\IRepositorySource;
use App\IRepository;
use Ricaefeliz\Mappero\Repositories\BaseRepository;
use Ricaefeliz\Mappero\ResultObjects\CountDTO;


class PersonalMeetingRepository extends BaseRepository implements IRepository, IRepositorySource
{


    /** @var string */
    protected $entityName = PersonalMeeting::class;



    /**
     * Get one by id.
     * @param $id int
     * @return PersonalMeeting
     * @throws PersonalMeetingNotFoundException
     */
    public function getOneById(int $id) : PersonalMeeting
    {
        $filters['where'][] = ['id', '=', $id];
        $personalMeeting = $this->findOneBy($filters);
        if (!$personalMeeting) {
            throw new OpportunityNotFoundException('Personal Meeting not found.');
        }
        return $personalMeeting;
    }

}
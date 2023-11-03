<?php

declare(strict_types = 1);

namespace App\Store\OpeningHours;

use App\Helpers\Entities;
use App\Store\OpeningHours\Change\ChangeRepository;
use Kdyby\Translation\ITranslator;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpeningHoursFacade
{


    /** @var ChangeRepository */
    private $changeRepo;

    /** @var OpeningHoursRepository */
    private $openingHoursRepo;

    /** @var ITranslator */
    private $translator;



    public function __construct(ChangeRepository $changeRepository,
                                ITranslator $translator,
                                OpeningHoursRepository $openingHoursRepository)
    {
        $this->changeRepo = $changeRepository;
        $this->openingHoursRepo = $openingHoursRepository;
        $this->translator = $translator;
    }



    /**
     * @return OpeningHoursDTO|null
     */
    public function getToday()
    {
        $date = new \DateTime();
        $change = $this->changeRepo->findOneByDate($date);
        if ($change) {
            return OpeningHoursDTO::createFromChange($change);
        }
        $day = $date->format('l');
        $openingHours = $this->openingHoursRepo->findOneByDay($day);
        return $openingHours ? OpeningHoursDTO::createFromOpeningHours($openingHours) : NULL;
    }



    /**
     * @return OpeningHours[]|array
     */
    public function findAll() : array
    {
        return $this->openingHoursRepo->findAll() ?: [];
    }



    /**
     * Return opening hours in week summary
     * @return array
     * todo test
     */
    public function getWeekList() : array
    {
        $list = [];
        $hours = $this->findAll();
        $hours = $hours ? Entities::setValueAsKey($hours, 'day') : [];
        $translationClosed = $this->translator->translate('store.openingHours.closed');
        $list[sprintf('%s-%s', $this->translator->translate('calendar.day.monday'), $this->translator->translate('calendar.day.thursday'))] = isset($hours['monday']) ? $hours['monday']->getFormattedHours() : $translationClosed;
        foreach (['friday', 'saturday', 'sunday', 'publicHolidays'] as $day) {
            $list[$this->translator->translate('calendar.day.' . $day)] = isset($hours[$day]) ? $hours[$day]->getFormattedHours() : $translationClosed;
        }
        return $list;
    }
}
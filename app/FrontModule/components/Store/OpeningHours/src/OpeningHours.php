<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\OpeningHours;

use App\Helpers\Entities;
use App\Store\OpeningHours\OpeningHoursDTO;
use App\Store\OpeningHours\OpeningHoursFacadeFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OpeningHours extends Control
{


    /** @var OpeningHoursFacadeFactory */
    private $openingHoursFacadeFactory;



    public function __construct(OpeningHoursFacadeFactory $openingHoursFacadeFactory)
    {
        parent::__construct();
        $this->openingHoursFacadeFactory = $openingHoursFacadeFactory;
    }



    public function renderToday()
    {
        $this->template->openingHours = $this->getOpeningHours();
        $this->template->setFile(__DIR__ . '/templates/today.latte');
        $this->template->render();
    }



    public function renderList()
    {
        $openingHoursFacade = $this->openingHoursFacadeFactory->create();
        $openingHours = $openingHoursFacade->getWeekList();

        $this->template->openingHours = $openingHours;
        $this->template->setFile(__DIR__ . '/templates/list.latte');
        $this->template->render();
    }


    public function renderListNoTable()
    {
        $openingHoursFacade = $this->openingHoursFacadeFactory->create();
        $openingHours = $openingHoursFacade->getWeekList();

        $this->template->openingHours = $openingHours;
        $this->template->setFile(__DIR__ . '/templates/listNoTable.latte');
        $this->template->render();
    }		
		

    /**
     * @return OpeningHoursDTO
     */
    private function getOpeningHours() : OpeningHoursDTO
    {
        $openingHoursFacade = $this->openingHoursFacadeFactory->create();
        return $openingHoursFacade->getToday() ?: new OpeningHoursDTO();
    }
}
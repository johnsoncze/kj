<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Contact;

use App\Store\OpeningHours\OpeningHoursFacadeFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class Contact extends Control
{


    /** @var OpeningHoursFacadeFactory */
    private $openingHoursFacadeFactory;



    public function __construct(OpeningHoursFacadeFactory $openingHoursFacadeFactory)
    {
        parent::__construct();
        $this->openingHoursFacadeFactory = $openingHoursFacadeFactory;
    }



    public function render()
    {
        $openingHoursFacade = $this->openingHoursFacadeFactory->create();
        $todayOpeningHours = $openingHoursFacade->getToday();
        $data = $this->getData();

        $this->template->openingHours = $todayOpeningHours;
        $this->template->telephone = $data['telephone'] ?? NULL;
        $this->template->email = $data['email'] ?? NULL;

        $this->template->setFile(__DIR__ . '/templates/basic.latte');
        $this->template->render();
    }



    public function renderBlock()
    {
        $openingHoursFacade = $this->openingHoursFacadeFactory->create();
        $todayOpeningHours = $openingHoursFacade->getToday();
        $data = $this->getData();

        $this->template->openingHours = $todayOpeningHours;
        $this->template->email = $data['email'] ?? NULL;
        $this->template->telephone = $data['telephone'] ?? NULL;

        $this->template->setFile(__DIR__ . '/templates/block.latte');
        $this->template->render();
    }



    /**
     * @return array
     */
    private function getData() : array
    {
        $parameters = $this->getPresenter()->context->getParameters();
        return $parameters['project'] ?? [];
    }
}
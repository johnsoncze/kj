<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PersonalMeetingList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\PersonalMeeting\PersonalMeeting;
use App\PersonalMeeting\PersonalMeetingRepository;
use Grido\Grid;
use Kdyby\Translation\ITranslator;
use Kdyby\Translation\Translator;
use Nette\Utils\Html;



class PersonalMeetingList extends GridoComponent
{


    /** @var Translator */
    protected $translator;

    /** @var PersonalMeetingRepository */
    protected $personalMeetingRepo;

		
		public function __construct(GridoFactory $gridoFactory,
                                ITranslator $translator,
                                PersonalMeetingRepository $personalMeetingRepository)
    {
        parent::__construct($gridoFactory);
        $this->translator = $translator;
        $this->personalMeetingRepo = $personalMeetingRepository;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
    //    $stateList = PersonalMeeting::getTranslatedStateList($this->translator);

        $source = new RepositorySource($this->personalMeetingRepo);
        $source->setDefaultSort('id', 'DESC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('lastName', 'Příjmení')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('firstName', 'Křestní jméno')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDate('addDate', 'Datum vytvoření')
            ->setDateFormat('d.m.Y H:i:s')
            ->setSortable()
            ->setFilterDateRange();

        //actions
        $grid->addActionHref('detail', '', ':' . $this->getPresenter()->getName() . ':detail')
            ->setIcon('eye');

				/*
        $grid->setRowCallback(function(Opportunity $row, Html $el){
            if ($row->getState() === Opportunity::STATE_NEW){
               $el->setAttribute('style', 'background-color:' . GridoComponent::HIGHLIGHT_ROW_BACKGROUND_COLOR);
            }
            return $el;
        });
				*/

        //styles
        $grid->getColumn('lastName')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('firstName')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('addDate')->getHeaderPrototype()->style['width'] = '20%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
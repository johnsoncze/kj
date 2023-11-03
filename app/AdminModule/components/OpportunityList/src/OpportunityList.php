<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OpportunityList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Opportunity\Opportunity;
use App\Opportunity\OpportunityRepository;
use Grido\Grid;
use Kdyby\Translation\ITranslator;
use Kdyby\Translation\Translator;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpportunityList extends GridoComponent
{


    /** @var Translator */
    protected $translator;

    /** @var OpportunityRepository */
    protected $opportunityRepo;

    /** @var array */
    protected $types = [];



    public function __construct(GridoFactory $gridoFactory,
                                ITranslator $translator,
                                OpportunityRepository $opportunityRepository)
    {
        parent::__construct($gridoFactory);
        $this->translator = $translator;
        $this->opportunityRepo = $opportunityRepository;
    }



    /**
     * @param $type string
     * @return self
     * @throws \InvalidArgumentException unknown type
     */
    public function addType(string $type) : self
    {
        if (!array_key_exists($type, Opportunity::getTypeList())) {
            throw new \InvalidArgumentException(sprintf('Unknown type \'%s\'.', $type));
        }
        $this->types[] = $type;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $stateList = Opportunity::getTranslatedStateList($this->translator);

        $source = new RepositorySource($this->opportunityRepo);
        $source->setDefaultSort('id', 'DESC');
        if ($this->types) {
            $source->filter([['type', '', $this->types]]);
        }

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('code', 'Kód')
            ->setFilterText();
        $grid->addColumnText('lastName', 'Příjmení')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('firstName', 'Křestní jméno')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('state', 'Stav')
            ->setReplacement($stateList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));
        $grid->addColumnDate('addDate', 'Datum vytvoření')
            ->setDateFormat('d.m.Y H:i:s')
            ->setSortable()
            ->setFilterDateRange();

        //actions
        $grid->addActionHref('detail', '', ':' . $this->getPresenter()->getName() . ':detail')
            ->setIcon('eye');

        $grid->setRowCallback(function(Opportunity $row, Html $el){
            if ($row->getState() === Opportunity::STATE_NEW){
               $el->setAttribute('style', 'background-color:' . GridoComponent::HIGHLIGHT_ROW_BACKGROUND_COLOR);
            }
            return $el;
        });

        //styles
        $grid->getColumn('code')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('lastName')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('firstName')->getHeaderPrototype()->style['width'] = '20%';
        $grid->getColumn('state')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('addDate')->getHeaderPrototype()->style['width'] = '15%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
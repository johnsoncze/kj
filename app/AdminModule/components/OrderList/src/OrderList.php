<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\OrderList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Order\Order;
use App\Order\OrderRepository;
use Grido\Grid;
use Kdyby\Translation\Translator;
use Nette\Utils\Html;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class OrderList extends GridoComponent
{


    /** @var OrderRepository */
    private $orderRepo;

    /** @var Translator */
    private $translator;



    public function __construct(GridoFactory $gridoFactory,
                                OrderRepository $orderRepository,
                                Translator $translator)
    {
        parent::__construct($gridoFactory);
        $this->orderRepo = $orderRepository;
        $this->translator = $translator;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $stateList = Order::getTranslatedStateList($this->translator);

        $source = new RepositorySource($this->orderRepo);
        $source->setDefaultSort('id', 'DESC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $code = $grid->addColumnText('code', 'Kód');
        $code->setSortable()->setFilterText();

        $lastName = $grid->addColumnText('customerLastName', 'Příjmení');
        $lastName->setSortable()->setFilterText();

        $firstName = $grid->addColumnText('customerFirstName', 'Křestní jméno');
        $firstName->setSortable()->setFilterText();

        $summaryPrice = $grid->addColumnNumber('summaryPrice', 'Cena celkem');
        $summaryPrice->setNumberFormat(2, ',', ' ');
        $summaryPrice->setSortable();

        $state = $grid->addColumnText('state', 'Stav');
        $state->setSortable()->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));
        $state->setReplacement($stateList);

        $addDate = $grid->addColumnDate('addDate', 'Datum vytvoření');
        $addDate->setSortable()->setFilterDateRange();
        $addDate->setDateFormat('d.m.Y H:i:s');

        //actions
        $grid->addActionHref('detail', '', 'Order:detail')
            ->setIcon('eye');

        $grid->setRowCallback(function (Order $row, Html $el) {
            if ($row->getState() === Order::NEW_STATE) {
                $el->setAttribute('style', 'background-color:' . GridoComponent::HIGHLIGHT_ROW_BACKGROUND_COLOR);
            }
            return $el;
        });

        //styles
        $code->getHeaderPrototype()->style['width'] = '15%';
        $lastName->getHeaderPrototype()->style['width'] = '17%';
        $firstName->getHeaderPrototype()->style['width'] = '17%';
        $summaryPrice->getHeaderPrototype()->style['width'] = '11%';
        $state->getHeaderPrototype()->style['width'] = '15%';
        $addDate->getHeaderPrototype()->style['width'] = '15%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
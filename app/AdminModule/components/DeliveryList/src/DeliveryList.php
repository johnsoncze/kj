<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\DeliveryList;

use App\Components\GridoComponent;
use App\Delivery\Delivery;
use App\Delivery\DeliveryRepositoryFactory;
use App\Delivery\Translation\DeliveryTranslation;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DeliveryList extends GridoComponent
{


    /** @var DeliveryRepositoryFactory */
    protected $deliveryRepoFactory;



    public function __construct(GridoFactory $gridoFactory,
                                DeliveryRepositoryFactory $deliveryRepositoryFactory)
    {
        parent::__construct($gridoFactory);
        $this->deliveryRepoFactory = $deliveryRepositoryFactory;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $deliveryAnnotation = DeliveryTranslation::getAnnotation();
        $table = $deliveryAnnotation->getTable();
        $name = $deliveryAnnotation->getPropertyByName('name');

        $states = Delivery::getStates();
        $stateList = Arrays::toPair($states, 'key', 'translation');

        $deliveryRepo = $this->deliveryRepoFactory->create();
        $source = new RepositorySource($deliveryRepo);
        $source->setDefaultSort('sort', 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        $grid->addColumnText('name', 'Název')
            ->setColumn(sprintf(':%s.%s', $table->getName(), $name->getColumn()->getName()))
            ->setCustomRender(function (Delivery $delivery) {
                $translation = $delivery->getTranslation();
                return $translation->getName();
            })
            ->setSortable()
            ->setFilterText();
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '50%';
        $grid->addColumnNumber('price', 'Cena')
            ->setNumberFormat(2, ',', ' ');
        $grid->addColumnNumber('vat', 'DPH')
            ->setNumberFormat(2, ',', ' ');
        $grid->getColumn('price')->getHeaderPrototype()->style['width'] = '15%';
        $grid->addColumnText('state', 'Stav')
            ->setReplacement($stateList)
            ->setSortable()
            ->setFilterSelect(Arrays::mergeTree(['' => ''], $stateList));

        //styles
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '45%';
        $grid->getColumn('price')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('vat')->getHeaderPrototype()->style['width'] = '15%';
        $grid->getColumn('state')->getHeaderPrototype()->style['width'] = '15%';

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductStateList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\ProductState\ProductState;
use App\ProductState\ProductStateRepositoryFactory;
use App\ProductState\Translation\ProductStateTranslation;
use Grido\Grid;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductStateList extends GridoComponent
{


    /** @var ProductStateRepositoryFactory */
    protected $productStateRepositoryFactory;



    public function __construct(GridoFactory $gridoFactory,
                                ProductStateRepositoryFactory $productStateRepositoryFactory)
    {
        parent::__construct($gridoFactory);
        $this->productStateRepositoryFactory = $productStateRepositoryFactory;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $productStateTranslationAnnotation = ProductStateTranslation::getAnnotation();
        $table = $productStateTranslationAnnotation->getTable();
        $valueProperty = $productStateTranslationAnnotation->getPropertyByName('value');

        $productStateRepo = $this->productStateRepositoryFactory->create();

        $source = new RepositorySource($productStateRepo);
        $source->setDefaultSort('sort', 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $grid->addColumnText('value', 'Název')
            ->setColumn(":{$table->getName()}.{$valueProperty->getColumn()->getName()}")
            ->setCustomRender(function (ProductState $productState) {
                return $productState->getTranslation()->getValue();
            })
            ->setSortable()
            ->setFilterText();

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
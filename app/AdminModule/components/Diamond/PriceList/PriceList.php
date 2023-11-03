<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Diamond\PriceList;

use App\Components\GridoComponent;
use App\Diamond\Price\PriceRepository;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PriceList extends GridoComponent
{


    /** @var PriceRepository */
    private $priceRepo;



    public function __construct(GridoFactory $gridoFactory,
                                PriceRepository $priceRepo)
    {
        parent::__construct($gridoFactory);
        $this->priceRepo = $priceRepo;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        $source = new RepositorySource($this->priceRepo);
        $source->setRepositoryMethod('findJoined');
        $source->setMethodCount('countJoined');
        $source->setDefaultSort('d_size', 'ASC');

        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $size = $grid->addColumnText('d_size', 'Velikost');
        $size->setSortable()->setFilterText();
        $size->getHeaderPrototype()->style['width'] = '10%';

        $type = $grid->addColumnText('d_type', 'Typ');
        $type->setSortable()->setFilterText();
        $type->getHeaderPrototype()->style['width'] = '10%';

        $quality = $grid->addColumnText('ppt_value', 'Kvalita');
        $quality->setSortable()->setFilterText();
        $quality->getHeaderPrototype()->style['width'] = '50%';

        $price = $grid->addColumnText('dp_price', 'Cena');
        $price->getHeaderPrototype()->style['width'] = '20%';
        $price->setCustomRender(function ($row) {
            return number_format($row['dp_price'], 2, ',', ' ') . ' Kč';
        });

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
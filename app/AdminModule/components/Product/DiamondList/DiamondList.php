<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Product\DiamondList;

use App\Components\GridoComponent;
use App\Extensions\Grido\GridoFactory;
use App\Extensions\Grido\RepositorySource;
use App\Helpers\Arrays;
use App\Product\Diamond\DiamondRepository;
use App\Product\Product;
use App\Product\WeedingRing\Gender\Gender;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class DiamondList extends GridoComponent
{


    /** @var Product|null */
    private $product;

    /** @var DiamondRepository */
    private $productDiamondRepo;



    public function __construct(DiamondRepository $diamondRepository,
                                GridoFactory $gridoFactory)
    {
        parent::__construct($gridoFactory);
        $this->productDiamondRepo = $diamondRepository;
    }



    /**
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    /**
     * @return Grid
     */
    public function createComponentList() : Grid
    {
        //repository
        $source = new RepositorySource($this->productDiamondRepo);
        $source->setMethodCount('countJoined');
        $source->setRepositoryMethod('findJoined');
        $source->filter([['productId', '=', $this->product->getId()]]);
        $source->setDefaultSort('d_size', 'ASC');

        //grid
        $grid = $this->gridoFactory->create();
        $grid->setModel($source);

        //columns
        $size = $grid->addColumnText('d_size', 'Velikost');
        $size->getHeaderPrototype()->style['width'] = '10%';
        $type = $grid->addColumnText('d_type', 'Typ');
        $type->getHeaderPrototype()->style['width'] = '10%';
        $quantity = $grid->addColumnText('pd_quantity', 'Množství');
        $quantity->getHeaderPrototype()->style['width'] = '10%';
        if ($this->product->isWeedingRingPair() === TRUE) {
            $genderList = Arrays::toPair(Gender::getTypes(), 'key', 'translation');
            $gender = $grid->addColumnText('pd_gender', 'Určení');
            $gender->setReplacement($genderList);
            $gender->getHeaderPrototype()->style['width'] = '10%';
        }

        return $grid;
    }



    public function render()
    {
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }
}
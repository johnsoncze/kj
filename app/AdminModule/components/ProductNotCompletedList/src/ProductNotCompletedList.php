<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductNotCompletedList;

use App\AdminModule\Components\ProductList\ProductList;
use App\Product\Product;
use Grido\Grid;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductNotCompletedList extends ProductList
{


    /**
     * @inheritdoc
     */
    public function createComponentList() : Grid
    {
        $grid = parent::createComponentList();
        $source = $grid->getModel();
        $source->filter([['completed', '=', FALSE]]);

        //columns
        $grid->addColumnText('commentCompleted', 'Poznámka k dokončení')
            ->setCustomRender(function(Product $product){
                return $product->getCommentCompleted() ?: '-';
            });

        //styles
        $grid->getColumn('name')->getHeaderPrototype()->style['width'] = '25%';
        $grid->getColumn('commentCompleted')->getHeaderPrototype()->style['width'] = '15%';

        return $grid;
    }
}
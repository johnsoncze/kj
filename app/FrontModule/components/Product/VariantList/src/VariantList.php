<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\VariantList;

use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\Variant\Tree\TreeFactory;
use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class VariantList extends Control
{


    /** @var Product|null */
    private $product;

    /** @var TreeFactory */
    private $variantTreeFactory;



    public function __construct(TreeFactory $treeFactory)
    {
        parent::__construct();
        $this->variantTreeFactory = $treeFactory;
    }



    /**
     * @param $product Product
     * @return self
     * @throws \InvalidArgumentException
     * @throws ProductNotFoundException
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    public function render()
    {
        $tree = $this->variantTreeFactory->createByProductId($this->product->getId());

        $this->template->tree = $tree;
        $this->template->product = $this->product;
        $this->template->mainProduct = $tree ? $tree->getMainProduct() : NULL;
        $this->template->setFile(__DIR__ . '/default.latte');
        $this->template->render();
    }


}
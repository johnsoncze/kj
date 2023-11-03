<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductVariantForm;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ProductTrait
{


    /** @var Product|null */
    private $product;



    /**
     * Set product.
     * @param $product Product
     * @return self
     */
    public function setProduct(Product $product) : self
    {
        $this->product = $product;
        return $this;
    }



    /**
     * @return Product
     * @throws \InvalidArgumentException if product is not set
     */
    public function getProduct() : Product
    {
        if (!$this->product instanceof Product) {
            throw new \InvalidArgumentException('You must set a product.');
        }
        return $this->product;
    }
}
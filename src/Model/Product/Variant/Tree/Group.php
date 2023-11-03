<?php

declare(strict_types = 1);

namespace App\Product\Variant\Tree;

use App\Product\ProductDTO;
use App\ProductParameterGroup\ProductParameterGroupEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Group
{


    /** @var ProductParameterGroupEntity */
    protected $parameterGroup;

    /** @var Variant[]|array */
    protected $variants = [];

    /** @var Variant|null */
    protected $mainVariant;

    /** @var int[]|array */
    protected $productsId = [];



    public function __construct(ProductParameterGroupEntity $parameterGroup)
    {
        $this->parameterGroup = $parameterGroup;
    }



    /**
     * @param $variant Variant
     * @return self
     */
    public function addVariant(Variant $variant) : self
    {
        $variantId = $variant->getVariant()->getId();
        if (!isset($this->variants[$variantId])) {
            $this->variants[$variantId] = $variant;
        }
        $this->addProductId($variant->getProduct());
        return $this;
    }



    /**
     * @param $variant Variant
     * @return self
     */
    public function setMainVariant(Variant $variant) : self
    {
        $this->mainVariant = $variant;
        $this->addProductId($variant->getProduct());
        return $this;
    }



    /**
     * @param $id int
     * @return Variant|null
     */
    public function getVariantById(int $id)
    {
        return $this->variants[$id] ?? NULL;
    }



    /**
     * @param $id int
     * @return bool
     */
    public function hasVariant(int $id) : bool
    {
        return isset($this->variants[$id]);
    }



    /**
     * @return Variant[]|array
     */
    public function getVariants() : array
    {
        $sorted = [];
        $variants = array_merge($this->variants, $this->mainVariant ? [$this->mainVariant] : []);
        /** @var $variants Variant[] */
        foreach ($variants as $variant) {
            $key = sprintf('%010d', $variant->getParameter()->getSort());
            $sorted[$key] = $variant;
        }
        ksort($sorted);
        return $sorted;
    }



    /**
     * @return ProductParameterGroupEntity
     */
    public function getParameterGroup() : ProductParameterGroupEntity
    {
        return $this->parameterGroup;
    }



    /**
     * @param $productId int
     * @return bool
     */
    public function hasProduct(int $productId) : bool
    {
        return in_array($productId, $this->productsId, TRUE);
    }



    /**
     * @param $product ProductDTO
     * @return ProductDTO
     */
    protected function addProductId(ProductDTO $product) : ProductDTO
    {
        $productId = $product->getProduct()->getId();
        if (!in_array($product, $this->productsId, TRUE)) {
            $this->productsId[] = $productId;
        }
        return $product;
    }
}
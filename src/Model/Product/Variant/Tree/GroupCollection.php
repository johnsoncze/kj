<?php

declare(strict_types = 1);

namespace App\Product\Variant\Tree;

use App\Product\ProductDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class GroupCollection implements IGroupCollection
{


    /** @var Group[]|array */
    protected $groups = [];

    /**
     * All variants of groups
     * @var Variant[]|array
     */
    protected $variants = [];

    /** @var ProductDTO */
    protected $mainProduct;



    public function __construct(ProductDTO $mainProduct)
    {
        $this->mainProduct = $mainProduct;
    }



    /**
     * @inheritdoc
     */
    public function addGroup(Group $group) : Group
    {
        $groupId = $group->getParameterGroup()->getId();
        if (!isset($this->groups[$groupId])) {
            $this->groups[$groupId] = $group;
        }
        return $group;
    }



    /**
     * @inheritdoc
     */
    public function getGroupById(int $id)
    {
        return $this->groups[$id] ?? NULL;
    }



    /**
     * @return Group[]|array
     */
    public function getGroups() : array
    {
        return $this->groups;
    }



    /**
     * @param $variant Variant
     * @return GroupCollection
     */
    public function addVariant(Variant $variant) : self
    {
        $variantId = $variant->getVariant()->getId();
        if (!isset($this->variants[$variantId])) {
            $this->variants[$variantId] = $variant;
        }
        return $this;
    }



    /**
     * @param $id int
     * @return GroupCollection
     */
    public function getVariantById(int $id)
    {
        return $this->variants[$id] ?? NULL;
    }



    /**
     * @return ProductDTO
     */
    public function getMainProduct() : ProductDTO
    {
        return $this->mainProduct;
    }
}
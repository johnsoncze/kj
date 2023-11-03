<?php

declare(strict_types = 1);

namespace App\Product\Variant\Tree;

use App\Product\ProductDTO;
use App\Product\Variant\Variant AS ProductVariant;
use App\ProductParameter\ProductParameterEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Variant implements IGroupCollection
{


    /** @var ProductDTO */
    protected $product;

    /** @var ProductParameterEntity */
    protected $parameter;

    /** @var ProductVariant|null */
    protected $variant;

    /** @var Group[]|array */
    protected $groups = [];



    public function __construct(ProductDTO $product,
                                ProductParameterEntity $parameter,
                                ProductVariant $variant = NULL)
    {
        $this->product = $product;
        $this->parameter = $parameter;
        $this->variant = $variant;
    }



    /**
     * @inheritdoc
     */
    public function addGroup(Group $group) : Group
    {
        $groupId = $group->getParameterGroup()->getId();
        $this->groups[$groupId] = $group;
        return $group;
    }



    /**
     * @return Group[]|array
     */
    public function getGroups() : array
    {
        return $this->groups;
    }



    /**
     * @return ProductDTO
     */
    public function getProduct() : ProductDTO
    {
        return $this->product;
    }



    /**
     * @return ProductParameterEntity
     */
    public function getParameter() : ProductParameterEntity
    {
        return $this->parameter;
    }



    /**
     * @return ProductVariant|null
     */
    public function getVariant()
    {
        return $this->variant;
    }



    /**
     * @inheritdoc
     */
    public function getGroupById(int $id)
    {
        return $this->groups[$id] ?? NULL;
    }



    /**
     * @param $productId int
     * @return bool
     */
    public function hasProductInTree(int $productId) : bool
    {
        return $this->hasGroupsProductRecursive($this->groups, $productId);
    }



    /**
     * @return bool
    */
    public function isMain() : bool
    {
        return $this->getVariant() === NULL;
    }



    /**
     * @param $groups Group[]
     * @param $productId int
     * @return bool
     */
    protected function hasGroupsProductRecursive(array $groups, int $productId) : bool
    {
        foreach ($groups as $group) {
            if ($group->hasProduct($productId)) {
                return TRUE;
            }

            $variants = $group->getVariants();
            foreach ($variants as $variant) {
                $variantGroups = $variant->getGroups();
                if ($variantGroups) {
                    return $this->hasGroupsProductRecursive($variantGroups, $productId);
                }
            }
        }

        return FALSE;
    }
}
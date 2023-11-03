<?php

declare(strict_types = 1);

namespace App\Product\Variant\Tree;

use App\Helpers\Entities;
use App\Product\Product;
use App\Product\ProductDTO;
use App\Product\ProductDTOFactory;
use App\Product\ProductNotFoundException;
use App\Product\ProductPublishedRepository;
use App\Product\ProductRepository;
use App\Product\Variant\Variant as ProductVariant;
use App\Product\Variant\VariantRepository;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TreeFactory
{


    /** @var ProductDTOFactory */
    protected $productDTOFactory;

    /** @var ProductRepository */
    protected $productRepo;

    /** @var ProductPublishedRepository */
    protected $productPublishedRepo;

    /** @var ProductParameterGroupRepository */
    protected $parameterGroupRepo;

    /** @var ProductParameterRepository */
    protected $parameterRepo;

    /** @var VariantRepository */
    protected $variantRepo;



    public function __construct(ProductDTOFactory $productDTOFactory,
                                ProductRepository $productRepo,
                                ProductPublishedRepository $productPublishedRepo,
                                ProductParameterGroupRepository $productParameterGroupRepo,
                                ProductParameterRepository $productParameterRepo,
                                VariantRepository $variantRepo)
    {
        $this->parameterGroupRepo = $productParameterGroupRepo;
        $this->parameterRepo = $productParameterRepo;
        $this->productDTOFactory = $productDTOFactory;
        $this->productRepo = $productRepo;
        $this->productPublishedRepo = $productPublishedRepo;
        $this->variantRepo = $variantRepo;
    }



    /**
     * @param $productId int
     * @param $published bool
     * @return GroupCollection|null
     * @throws ProductNotFoundException
     */
    public function createByProductId(int $productId, bool $published = TRUE)
    {
        $variants = $this->getVariants($productId, $published);
        if ($variants) {
            $mainProduct = $this->getMainProduct(end($variants));
            $products = $this->getProducts($variants, $mainProduct, $published);
            if (isset($products[$mainProduct->getId()])) {
                $groupCollection = new GroupCollection($products[$mainProduct->getId()]);
            } else {
                $groupCollection = new GroupCollection(end($products));
            }
            $parameterGroups = $this->getParameterGroups($variants);
            $parameters = $this->getParameters($variants);
            $this->buildGroupCollection($groupCollection, $products, $parameterGroups, $parameters, $variants);
        }
        return $groupCollection ?? NULL;
    }



    /**
     * @param $groupCollection GroupCollection
     * @param $products ProductDTO[]|array
     * @param $parameterGroups ProductParameterGroupEntity[]
     * @param $parameters ProductParameterEntity[]
     * @param $variants ProductVariant[]
     * @return GroupCollection
     */
    protected function buildGroupCollection(GroupCollection $groupCollection,
                                            array $products,
                                            array $parameterGroups,
                                            array $parameters,
                                            array $variants) : GroupCollection
    {
        $variantList = array_replace($variants, [
            'segmentedByParameterGroup' => Entities::toSegment($variants, 'parameterGroupId'),
        ]);

        foreach ($variants as $variant) {
            $this->processVariantRecursive($variant, $variantList, $parameterGroups, $parameters, $products, $groupCollection);
        }

        return $groupCollection;
    }



    /**
     * @param $variant ProductVariant
     * @param $variants ProductVariant[]
     * @param $parameterGroups ProductParameterGroupEntity[]
     * @param $parameters ProductParameterEntity[]
     * @param $products ProductDTO[]
     * @param $groupCollection GroupCollection
     * @return Variant
     */
    protected function processVariantRecursive(ProductVariant $variant,
                                               array $variants,
                                               array $parameterGroups,
                                               array $parameters,
                                               array $products,
                                               GroupCollection $groupCollection)
    {
        $parentVariantId = $variant->getParentVariantId();
        if ($parentVariantId === null) {
            if (!isset($products[$variant->getProductId()])) {
                return null;
            }
            $group = $this->getOrCreateGroup($groupCollection, $variant->getParameterGroupId(), $parameterGroups[$variant->getParameterGroupId()]);
            $this->addMainProductToGroup($group, $products[$variant->getProductId()], $this->getMainProductParameter($group->getParameterGroup(), $variants, $parameters));
        } else {
            $parentVariant = $groupCollection->getVariantById($parentVariantId);
            if ($parentVariant === null) {
                $parentVariant = $this->processVariantRecursive($variants[$parentVariantId], $variants, $parameterGroups, $parameters, $products, $groupCollection);
            }
            $group = $this->getOrCreateGroup($parentVariant, $variant->getParameterGroupId(), $parameterGroups[$variant->getParameterGroupId()]);
        }
        
        $treeVariant = $group->getVariantById($variant->getId());
        if ($treeVariant === null) {
            $treeVariant = new Variant($products[$variant->getProductVariantId()], $parameters[$variant->getProductVariantParameterId()], $variant);
            $group->addVariant($treeVariant);
            $groupCollection->addVariant($treeVariant);
        }

        return $treeVariant;
    }



    /**
     * Get variants of product.
     * @param $productId int
     * @param $published bool
     * @return ProductVariant[]|array
     */
    protected function getVariants(int $productId, bool $published = TRUE) : array
    {
        //find variants as $product is main product
        $variants = $published ? $this->variantRepo->findPublishedVariantsByProductId($productId) : $this->variantRepo->findByProductId($productId);

        if (!$variants) { //find variants as $product is a variant
            $variant = $this->variantRepo->findOneByProductVariantId($productId);
            if ($variant) {
                $variants = $published ? $this->variantRepo->findPublishedVariantsByProductId($variant->getProductId()) : $this->variantRepo->findByProductId($variant->getProductId());
            }
        }

        return $variants;
    }



    /**
     * @param $variant ProductVariant
     * @return Product
     * @throws ProductNotFoundException
     */
    protected function getMainProduct(ProductVariant $variant) : Product
    {
        $mainProductId = $variant->getProductId();
        return $this->productRepo->getOneById($mainProductId);
    }



    /**
     * @param $variants Variant[]
     * @return ProductParameterGroupEntity[]
     */
    protected function getParameterGroups(array $variants) : array
    {
        $groupId = Entities::getProperty($variants, 'parameterGroupId');
        return $this->parameterGroupRepo->findByMoreId($groupId);
    }



    /**
     * @param $variants Variant[]
     * @return ProductParameterEntity[]
     */
    protected function getParameters(array $variants) : array
    {
        $parametersId = Entities::getProperty($variants, 'productVariantParameterId');
        $mainProductParametersId = Entities::getProperty($variants, 'productParameterId');
        return $this->parameterRepo->findByMoreId(array_merge($parametersId, $mainProductParametersId));
    }



    /**
     * @param $variants Variant[]
     * @param $mainProduct Product
     * @param $published bool
     * @return ProductDTO[]
     */
    protected function getProducts(array $variants, Product $mainProduct, bool $published = FALSE) : array
    {
        $productId = Entities::getProperty($variants, 'productVariantId');
        $productRepo = $published ? $this->productPublishedRepo : $this->productRepo;
        $products = $productRepo->findByMoreId(array_merge($productId, [$mainProduct->getId()]));
        return $this->productDTOFactory->createFromProducts($products);
    }



    /**
     * @param $groupCollection IGroupCollection
     * @param $groupId int
     * @param $parameterGroup ProductParameterGroupEntity
     * @return Group
     */
    protected function getOrCreateGroup(IGroupCollection $groupCollection, int $groupId, ProductParameterGroupEntity $parameterGroup) : Group
    {
        $group = $groupCollection->getGroupById($groupId);
        if ($group === NULL) {
            $group = new Group($parameterGroup);
            $groupCollection->addGroup($group);
        }
        return $group;
    }



    /**
     * @param $group Group
     * @param $mainProduct ProductDTO
     * @param $parameter ProductParameterEntity
     * @return Group
     */
    protected function addMainProductToGroup(Group $group, ProductDTO $mainProduct, ProductParameterEntity $parameter) : Group
    {
        if ($group->hasProduct($mainProduct->getProduct()->getId()) !== TRUE) {
            $variant = new Variant($mainProduct, $parameter);
            $group->setMainVariant($variant);
        }
        return $group;
    }



    /**
     * @param $parameterGroup ProductParameterGroupEntity
     * @param $variants ProductVariant[]
     * @param $parameters ProductParameterEntity[]
     * @return ProductParameterEntity
     */
    protected function getMainProductParameter(ProductParameterGroupEntity $parameterGroup, array $variants, array $parameters)
    {
        /** @var $variant ProductVariant */
        $variant = end($variants['segmentedByParameterGroup'][$parameterGroup->getId()]);
        return $parameters[$variant->getProductParameterId()];
    }
}
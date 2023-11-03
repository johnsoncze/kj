<?php

declare(strict_types = 1);

namespace App\Product;

use App\Category\Product\Related\ProductRepository AS CollectionListProductRepository;
use App\Category\Product\Sorting\SortingRepository;
use App\FrontModule\Components\Category\Filtration\Filter\SortFilter;
use App\Helpers\Entities;
use App\Product\Related\RelatedRepository;
use App\Product\Variant\VariantRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductFindFacade
{


    /** @var SortingRepository */
    private $categoryProductSortingRepo;

    /** @var CollectionListProductRepository */
    private $collectionListProductRepo;

    /** @var ProductDTOFactory */
    private $productDTOFactory;

    /** @var ProductMasterFinder */
    private $productMasterFinder;

    /** @var ProductPublishedRepository */
    private $productPublishedRepo;

    /** @var ProductRepository */
    private $productRepo;

    /** @var VariantRepository */
    private $productVariantRepo;

    /** @var RelatedRepository */
    private $relatedRepo;



    public function __construct(CollectionListProductRepository $collectionListProductRepository,
                                ProductDTOFactory $productDTOFactory,
                                ProductMasterFinder $productMasterFinder,
                                ProductPublishedRepository $productPublishedRepo,
                                ProductRepository $productRepo,
                                RelatedRepository $relatedRepository,
                                SortingRepository $sortingRepo,
                                VariantRepository $variantRepo)
    {
        $this->collectionListProductRepo = $collectionListProductRepository;
        $this->productDTOFactory = $productDTOFactory;
        $this->productMasterFinder = $productMasterFinder;
        $this->productPublishedRepo = $productPublishedRepo;
        $this->productRepo = $productRepo;
        $this->productVariantRepo = $variantRepo;
        $this->relatedRepo = $relatedRepository;
        $this->categoryProductSortingRepo = $sortingRepo;
    }



    /**
     * @param $categoryId int[]
     * @param $type string
     * @return ProductDTO[]|array
     */
    public function findRepresentativePublishedByMoreCategoryIdAndType(array $categoryId, string $type) : array
    {
        $products = [];
        $representativeProducts = $this->collectionListProductRepo->findPublishedByMoreCategoryIdAndType($categoryId, $type);
        if ($representativeProducts) {
            $productId = Entities::getProperty($representativeProducts, 'productId');
            $catalogProducts = $this->productPublishedRepo->findByMoreId($productId);
            $productsDTO = $catalogProducts ? $this->productDTOFactory->createFromProducts($catalogProducts) : [];
            foreach ($representativeProducts as $representativeProduct) {
                $productDTO = $productsDTO[$representativeProduct->getProductId()] ?? NULL;
                if ($productDTO !== NULL) {
                    $products[$representativeProduct->getCategoryId()][$representativeProduct->getProductId()] = $productDTO;
                }
            }
        }
        return $products;
    }



    /**
     * @param $productId array
     * @param $type int|string
     * @return ProductDTO[]|array
     */
    public function findPublishedRelatedProductsByMoreProductIdAndType(array $productId, $type) : array
    {
        $_products = [];
        $masterProductId = $this->getMasterProductId($productId);
        $relatedProducts = $masterProductId ? $this->relatedRepo->findByMoreProductIdAndType($masterProductId, $type) : [];
        if ($relatedProducts) {
            $catalogProductId = Entities::getProperty($relatedProducts, 'relatedProductId');
            $products = $this->productPublishedRepo->findByMoreId($catalogProductId);
            $_products = $this->productDTOFactory->createFromProducts($products);
        }
        return $_products;
    }



    /**
     * @param $productId array
     * @return array
     */
    public function findPublishedMinAndMaxPriceByMoreProductId(array $productId) : array
    {
        return $this->productPublishedRepo->findMinAndMaxPriceByMoreProductId($productId);
    }



    /**
     * Count by search.
     * @param $languageId int
     * @param $query string
     * @param $filter array
     * @return array
     */
    public function findPublishedMoreIdBySearch(int $languageId, string $query, array $filter = []) : array
    {
        return $this->productPublishedRepo->findMoreIdBySearch($languageId, $query, $filter) ?: [];
    }



    /**
     * Find by search.
     * @param $productId array
     * @param $limit int
     * @param $offset int
     * @param $filter array
     * @param $categoryId int|null
     * @return ProductDTO[]|array
     */
    public function findPublishedByMoreIdAndLimitAndOffset(array $productId, int $limit, int $offset, array $filter = [], int $categoryId = NULL) : array
    {
        if ($categoryId !== NULL && !isset($filter[SortFilter::KEY])) {
            $filter[SortFilter::KEY] = $this->categoryProductSortingRepo->findProductIdByCategoryId($categoryId);
        }
        $products = $this->productPublishedRepo->findByMoreIdAndOffsetAndLimit($productId, $offset, $limit, $filter);
        return $products ? $this->productDTOFactory->createFromProducts($products) : [];
    }



    /**
     * @param $url string
     * @return ProductDTO
     * @throws ProductNotFoundException
     */
    public function getOnePublishedByUrl(string $url) : ProductDTO
    {
        $product = $this->productPublishedRepo->getOneByUrl($url);
        $productsDTO = $this->productDTOFactory->createFromProducts([$product], TRUE, TRUE);
        return end($productsDTO);
    }



    /**
     * Find master product by a product id.
     * @param $id int
     * @return Product|null
     */
    public function findMaster(int $id)
    {
        try {
            return $this->productMasterFinder->findOneByProductId($id);
        } catch (ProductNotFoundException $exception) {
            return NULL;
        }
    }



    /**
     * @param $productId int
     * @param $parameterGroupId int
     * @return Product[]|array
     */
    public function findVariantsByProductIdAndParameterGroupId(int $productId, int $parameterGroupId) : array
    {
        $variantProducts = [];
        $variants = $this->productVariantRepo->findByProductIdAndParameterGroupId($productId, $parameterGroupId);
        if ($variants) {
            $productId = Entities::getProperty($variants, 'productVariantId');
            $products = $this->productRepo->findByMoreId($productId);
            foreach ($variants as $variant) {
                $variantProduct = $products[$variant->getProductVariantId()];
                $variantProducts[$variant->getProductVariantParameterId()] = $variantProduct;
            }
        }
        return $variantProducts;
    }



    /**
     * @param $productId array
     * @param $parameterId array [parameterId => groupId,..]
     * @return array
     */
    public function findProductIdWithGroupedVariantsByMoreProductIdAndMoreParameterId(array $productId, array $parameterId = []) : array
    {
        $productsStock = $this->productPublishedRepo->findStock();
        $groupedVariants = $this->productVariantRepo->findAllGrouped();

        $variantProducts = $this->removeAndGetVariantProducts($productId, $groupedVariants);
        $preferredVariants = $this->getPreferredVariants($variantProducts, $productsStock, $groupedVariants, $parameterId);
        $this->addPreferredVariants($productId, $preferredVariants);

        return $productId;
    }



    /**
     * @param $productId int[]
     * @param $variantParameterId int[]|array parameters id which should not be represented by main product
     * @return array|int[]
     */
    public function findProductIdWithGroupedVariantsByProductId(array $productId, array $variantParameterId = []) : array
    {
        return $this->productPublishedRepo->findProductIdWithGroupedVariantsByProductId($productId, $variantParameterId);
    }



    /**
     * @param $productId array
     * @param $groupId int
     * @return array
     */
    public function findProductIdWithNonGroupedVariantsFromGroupByMoreProductIdAndGroupId(array $productId, int $groupId) : array
    {
        return $this->productPublishedRepo->findProductIdWithNonGroupedVariantsFromGroupByMoreProductIdAndGroupId($productId, $groupId);
    }



    /**
     * @param $productId array
     * @return array
     */
    private function getMasterProductId(array $productId) : array
    {
        $masterProductId = [];
        foreach ($productId as $id) {
            try {
                $master = $this->productMasterFinder->findOneByProductId($id);
                $masterProductId[] = $master ? $master->getId() : $id;
            } catch (ProductNotFoundException $exception) {
                //nothing..
            }
        }
        return $masterProductId;
    }



    /**
     * Remove and get variant products.
     * @param $productId array
     * @param $variantGroups array
     * @return array
     */
    private function removeAndGetVariantProducts(array &$productId, array $variantGroups = []) : array
    {
        $variantProducts = [];
        foreach ($productId as $key => $id) {

            //resolve main product
            $mainProduct = $variantGroups['groups'][$id] ?? NULL;
            if ($mainProduct) {
                if (!isset($variantProducts[$id])) {
                    $variantProducts[$id][$id] = $id;
                }
                unset($productId[$key]);
                continue;
            }

            //resolve variant
            $variantMainProduct = $variantGroups['variantList']['byProductVariant'][$id] ?? NULL;
            if ($variantMainProduct) {
                if (!isset($variantProducts[$variantMainProduct])) {
                    $variantProducts[$variantMainProduct] = [];
                }
                $variantProducts[$variantMainProduct][$id] = $id;
                unset($productId[$key]);
            }
        }
        return $variantProducts;
    }



    /**
     * Resolve preferred variants.
     *
     * @param $variantProducts array
     * @param $productsStock array
     * @param $groupedVariants array
     * @param $filteredParameters array
     * @return array
     */
    private function getPreferredVariants(array $variantProducts,
                                          array $productsStock,
                                          array $groupedVariants,
                                          array $filteredParameters = []) : array
    {
        $preferredVariants = [];
        $filteredGroups = array_flip($filteredParameters);
        foreach ($variantProducts as $mainProductId => $mainProductVariants) {
            $hasFilteredGroup = FALSE;
            $mainProductStock = $productsStock[$mainProductId] ?? NULL; //null means that product is not published
            $mainProductGroupedVariants = $groupedVariants['groups'][$mainProductId] ?? [];

            $iterator = 0;
            foreach ($mainProductGroupedVariants as $groupId => $groupVariants) {
                if (isset($filteredGroups[$groupId])) {
                    if ($iterator > 1) {
                        break;
                    }
                    $iterator++;
                }
            }

            $hasMoreFilteredGroups = $iterator > 1;
            foreach ($mainProductGroupedVariants as $groupId => $groupVariants) {
                if (!isset($filteredGroups[$groupId])) {
                    continue; //do not process group which is not filtered
                }

                $hasFilteredGroup = TRUE;
                $lastVariant = end($groupVariants);

                //add main product if is required as well
                if ($mainProductStock !== NULL && isset($filteredParameters[$lastVariant['pv_product_parameter_id']], $mainProductVariants[$mainProductId])) {
                    $preferredVariants[$mainProductId][$groupId][$lastVariant['pv_product_parameter_id']] = $mainProductId;
                }

                foreach ($groupVariants as $variant) {
                    if (isset($filteredParameters[$variant['pv_product_variant_parameter_id']], $mainProductVariants[$variant['pv_product_variant_id']])) {
                        $parentVariantParameterKey = $hasMoreFilteredGroups ? $this->createParentVariantParameterKey($variant, $groupedVariants['variantList']['all']) : NULL;
                        $variantStock = $productsStock[$variant['pv_product_variant_id']] ?? NULL; //null means that product is not published
                        $preferredVariant = $preferredVariants[$mainProductId][$groupId][$parentVariantParameterKey ?: $variant['pv_product_variant_parameter_id']] ?? NULL;
                        $preferredVariantStock = $preferredVariant ? $productsStock[$preferredVariant] : NULL;
                        $variantStock && $hasStockVariant = TRUE;

                        if ($variantStock !== NULL && ($preferredVariant === NULL || $preferredVariantStock < $variantStock)) {
                            $preferredVariants[$mainProductId][$groupId][$parentVariantParameterKey ?: $variant['pv_product_variant_parameter_id']] = $variant['pv_product_variant_id'];
                        }
                    }
                }
            }

            if ($hasFilteredGroup === FALSE) {
                $groupId = $parameterId = 0; //for set only a one product
                $mainProductStock !== NULL && $preferredVariants[$mainProductId][$groupId][$parameterId] = $mainProductId;
                $variants = $groupedVariants['variantList']['byMainProduct'][$mainProductId] ?? [];

                foreach ($variants as $variantId) {
                    $variantStock = $productsStock[$variantId] ?? NULL;
                    $preferredVariant = $preferredVariants[$mainProductId][$groupId][$parameterId] ?? NULL;
                    $preferredVariantStock = $preferredVariant ? $productsStock[$preferredVariant] : NULL;

                    if ($variantStock !== NULL && ($preferredVariant === NULL || $preferredVariantStock < $variantStock)) {
                        $preferredVariants[$mainProductId][$groupId][$parameterId] = $variantId;
                    }
                }
            }
        }
        return $preferredVariants;
    }



    /**
     * Add preferred variants to array.
     *
     * @param $productId array
     * @param $preferredVariants array
     * @return array
     */
    private function addPreferredVariants(array &$productId, array $preferredVariants) : array
    {
        foreach ($preferredVariants as $mainProductId => $variantsGroups) {
            foreach ($variantsGroups as $groupId => $parametersId) {
                foreach ($parametersId as $parameterId => $variantId) {
                    $productId[$variantId] = $variantId;
                }
            }
        }

        return $productId;
    }



    /**
     * @param $variant array
     * @param $variants array
     * @param $key string|null
     * @return string|null
    */
    private function createParentVariantParameterKey(array $variant, array $variants, string $key = NULL)
    {
        $parentVariantId = $variant['pv_parent_variant_id'];
        if ($parentVariantId) {
            $parentVariant = $variants[$parentVariantId];
            $key .= $parentVariant['pv_product_variant_parameter_id'];
            return $this->createParentVariantParameterKey($parentVariant, $variants, $key);
        }
        return $key;
    }
}
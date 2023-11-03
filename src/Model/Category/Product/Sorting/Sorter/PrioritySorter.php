<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting\Sorter;

use App\Category\CategoryEntity;
use App\Category\Product\Related\ProductRepository AS CategoryRelatedProductRepository;
use App\Category\Product\Sorting\Sorting;
use App\Category\Product\Sorting\SortingRepository;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\Helpers\Entities;
use App\Product\Product;
use App\Product\ProductRepository;
use App\Product\Variant\VariantRepository;
use Kdyby\Monolog\Logger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class PrioritySorter extends BaseSorter
{


    use NoveltyTrait;
    use StockTrait;

    /** @var CategoryRelatedProductRepository */
    protected $categoryRelatedProductRepo;

    /** @var array */
    protected $priorityProducts = [];

    /** @var VariantRepository */
    protected $variantRepo;



    public function __construct(CategoryProductParameterRepository $categoryParameterRepo,
                                CategoryRelatedProductRepository $categoryRelatedProductRepo,
                                Logger $logger,
                                ProductRepository $productRepo,
                                SortingRepository $sortingRepo,
								VariantRepository $variantRepo)
    {
        parent::__construct($categoryParameterRepo, $logger, $productRepo, $sortingRepo);
        $this->categoryRelatedProductRepo = $categoryRelatedProductRepo;
        $this->variantRepo = $variantRepo;
    }



    /**
     * @inheritdoc
     */
    public function execute(CategoryEntity $category)
    {
        $this->priorityProducts = $this->getPriorityProductList($category);
        parent::execute($category);
    }



    /**
     * @inheritdoc
     */
    protected function resolveSorting(Product $product) : Sorting
    {
        $prioritySorting = $this->getPrioritySorting($product);
        $noveltySorting = $this->getNoveltySorting($product);
        $stockSorting = $this->getStockSorting($product);

        $sorting = $this->createSortingHash($prioritySorting, $noveltySorting, $stockSorting);
        return $this->createSortingObject($product, $sorting);
    }



    /**
     * @param $product Product
     * @return int
     */
    protected function getPrioritySorting(Product $product) : int
    {
        return $this->priorityProducts[$product->getId()] ?? $this->toEnd(10);
    }



    /**
     * Get priority product list.
     * @param $category CategoryEntity
     * @return array in format [productId => sorting,..] if some exists
     */
    protected function getPriorityProductList(CategoryEntity $category) : array
    {
    	$priorityList = [];
        $categoryId = [$category->getId()];
        $type = \App\Category\Product\Related\Product::TYPE_SORTING_PRODUCT;
        $products = $this->categoryRelatedProductRepo->findPublishedByMoreCategoryIdAndType($categoryId, $type);

        if ($products) {
        	$priorityList = Entities::toPair($products, 'productId', 'sort');
        	unset($products);

        	//product variants have same priority as their main product
        	foreach ($priorityList as $productId => $sorting) {
        		$variants = $this->variantRepo->findByProductId($productId);
        		foreach ($variants as $key => $variant) {
        			$priorityList[$variant->getProductVariantId()] = $sorting;
        			unset($variants[$key]);
				}
			}
		}

        return $priorityList;
    }
}
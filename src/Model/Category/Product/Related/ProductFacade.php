<?php

declare(strict_types = 1);

namespace App\Category\Product\Related;

use App\Category\CategoryEntity;
use App\Category\CategoryNotFoundException;
use App\Category\CategoryRepository;
use App\Category\Product\Related\Product AS CollectionListProduct;
use App\Category\Product\Related\ProductRepository AS CollectionListProductRepository;
use App\NotFoundException;
use App\Product\Product;
use App\Product\ProductNotFoundException;
use App\Product\ProductRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
final class ProductFacade
{


    /** @var CategoryRepository */
    private $categoryRepo;

    /** @var CollectionListProductRepository */
    private $collectionListProductRepo;

    /** @var ProductRepository */
    private $productRepo;



    public function __construct(CategoryRepository $categoryRepository,
                                CollectionListProductRepository $collectionListProductRepository,
                                ProductRepository $productRepository)
    {
        $this->categoryRepo = $categoryRepository;
        $this->collectionListProductRepo = $collectionListProductRepository;
        $this->productRepo = $productRepository;
    }



    /**
     * @param $categoryId int
     * @param $productId int
	 * @param $type string
     * @return CollectionListProduct
     * @throws ProductFacadeException
     */
    public function add(int $categoryId, int $productId, string $type) : CollectionListProduct
    {
        $duplicateProduct = $this->collectionListProductRepo->findOneByCategoryIdAndProductIdAndType($categoryId, $productId, $type);
        if ($duplicateProduct) {
            throw new ProductFacadeException('Produkt je již přidán.');
        }

        try {
            $category = $this->categoryRepo->getOneById($categoryId);
            $product = $this->productRepo->getOneById($productId);
            $collectionProduct = $this->createCollectionListProduct($product, $category, $type);
            $this->collectionListProductRepo->save($collectionProduct);
            return $collectionProduct;
        } catch (ProductNotFoundException $exception) {
            throw new ProductFacadeException($exception->getMessage());
        } catch (CategoryNotFoundException $exception) {
			throw new ProductFacadeException($exception->getMessage());
		}
    }



    /**
     * @param $id int
     * @return bool
     * @throws ProductFacadeException
     */
    public function remove(int $id) : bool
    {
        try {
            $collectionProduct = $this->collectionListProductRepo->getByMoreId([$id]);
            $this->collectionListProductRepo->remove(end($collectionProduct));
            return TRUE;
        } catch (NotFoundException $exception) {
            throw new ProductFacadeException($exception->getMessage());
        }
    }



    /**
	 * @param $sorting int[] [databaseRowId => sorting,..]
	 * @return void
	 * @throws ProductFacadeException
    */
	public function saveSorting(array $sorting)
	{
		$id = array_keys($sorting);
		$products = $this->collectionListProductRepo->findByMoreId($id);

		foreach ($sorting as $id => $s) {
			$product = $products[$id] ?? NULL;
			if ($product === NULL) {
				throw new ProductFacadeException(sprintf('Položka s id \'%d\' nebyla nalezena. Seřaďte produkty znovu.', $id));
			}
			$product->setSort($s);
			$this->collectionListProductRepo->save($product);
			unset($product);
		}
	}



    /**
     * @param $product Product
     * @param $category CategoryEntity
	 * @param $type string
     * @return CollectionListProduct
     */
    private function createCollectionListProduct(Product $product, CategoryEntity $category, string $type) : CollectionListProduct
    {
        $collectionProduct = new CollectionListProduct();
        $collectionProduct->setProductId($product->getId());
        $collectionProduct->setCategoryId($category->getId());
        $collectionProduct->setType($type);
        return $collectionProduct;
    }
}
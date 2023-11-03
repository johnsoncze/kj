<?php

declare(strict_types = 1);

namespace App\Product;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductDuplication
{


	/**
	 * Check if exists some product with same external system id.
	 * @param $product Product
	 * @param $productRepo ProductRepository
	 * @return Product
	 * @throws ProductDuplicationException
	 */
	public function checkByExternalSystemId(Product $product, ProductRepository $productRepo) : Product
	{
		$duplicateProduct = $productRepo->findOneByExternalSystemId((int)$product->getExternalSystemId());
		if ($duplicateProduct !== NULL && (int)$duplicateProduct->getId() !== (int)$product->getId()) {
			$message = sprintf('Produkt s id \'%d\' externího systému již existuje. A to \'%s - %s\'.',
				$product->getExternalSystemId(), $duplicateProduct->getCode(), $duplicateProduct->getTranslation()->getName());
			throw new ProductDuplicationException($message);
		}
		return $product;
	}



	/**
	 * Check if exists some product with same code.
	 * @param $product Product
	 * @param $productRepository ProductRepository
	 * @return Product
	 * @throws ProductDuplicationException
	 */
	public function checkByCode(Product $product, ProductRepository $productRepository) : Product
	{
		$duplicateProduct = $productRepository->findOneByCode($product->getCode());
		if ($duplicateProduct && $duplicateProduct->getId() !== $product->getId()) {
			$message = sprintf('Produkt s kódem \'%s\' již existuje.', $product->getCode());
			throw new ProductDuplicationException($message);
		}
		return $product;
	}
}
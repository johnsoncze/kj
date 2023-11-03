<?php

declare(strict_types = 1);

namespace App\Google\TagManager\EnhancedEcommerce;

use App\ShoppingCart\ShoppingCartDTO;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class DataFactory
{


	/**
	 * @param $cart ShoppingCartDTO
	 * @param $step int
	 * @param $additionalData array
	 * @return array
	 */
	public static function create(ShoppingCartDTO $cart, int $step = 1, array $additionalData = []) : array
	{
		$additionalData['step'] = $step;
		$products = $cart->getProducts();

		//basic data
		$data['event'] = 'eec.checkout';
		$data['ecommerce']['checkout']['actionField'] = $additionalData;

		//product data
		foreach ($products as $product) {
			if ($product->getProductId() && ($productDTO = $cart->getProductDTOByProductId($product->getProductId()))) {
				$data['ecommerce']['products'][] = [
					'name' => $product->getName(),
					'id' => $product->getCatalogProduct()->getCode(),
					'price' => $product->getUnitPriceWithoutVat(),
					'brand' => $productDTO->getBrand() ? $productDTO->getBrand()->getName() : NULL,
					'category' => $productDTO->getCategory() ? $productDTO->getCategory()->getTextNavigation() : NULL,
					'quantity' => $product->getQuantity(),
				];
			}
		}

		return $data;
	}
}
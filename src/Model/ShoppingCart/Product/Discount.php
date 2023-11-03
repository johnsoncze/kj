<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\ShoppingCart\ShoppingCart;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Discount
{


	/**
	 * @param $shoppingCart ShoppingCart
	 * @param $product ShoppingCartProduct
	 * @return ShoppingCartProduct
	 * @throws \InvalidArgumentException
	 * todo test
	 */
	public function applyDiscount(ShoppingCart $shoppingCart,
								  ShoppingCartProduct $product) : ShoppingCartProduct
	{
		$discount = $shoppingCart->getDiscount();
		$catalogProduct = $product->getCatalogProduct();
		if ($catalogProduct) {
			$discount = $product->getCatalogProduct()->isDiscountAllowed() ? $discount : 0;
      $discount = ($shoppingCart->getCustomerId() !== null && $product->getCatalogProduct()->getTmpDiscount()) ? 25 : $discount;
			$discount ? $product->setDiscount($discount) : $product->removeDiscount();
		}

		return $product;
	}
}
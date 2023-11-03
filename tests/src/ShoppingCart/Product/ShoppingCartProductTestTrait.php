<?php

declare(strict_types = 1);

namespace App\Tests\ShoppingCart\Product;

use App\ShoppingCart\Product\ShoppingCartProduct;
use App\ShoppingCart\ShoppingCartHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ShoppingCartProductTestTrait
{


	/**
	 * @return ShoppingCartProduct
	 */
	private function createTestShoppingCartProduct() : ShoppingCartProduct
	{
		$product = new ShoppingCartProduct();
		$product->setName('product');
		$product->setShoppingCartId(5);
		$product->setProductId(77);
		$product->setPrice(450.50);
		$product->setVat(21.00);
		$product->setQuantity(55);
		$product->setDiscount(15.00);
		$product->setHash(ShoppingCartHash::generateHash());

		return $product;
	}
}
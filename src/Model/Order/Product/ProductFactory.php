<?php

declare(strict_types = 1);

namespace App\Order\Product;

use App\Order\Order;
use App\ShoppingCart\Product\ShoppingCartProduct;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFactory
{


    /**
     * @param $order Order
     * @param $shoppingCartProduct ShoppingCartProduct
     * @return Product
     * @throws \InvalidArgumentException
     */
    public function createByCartProduct(Order $order,
                                        ShoppingCartProduct $shoppingCartProduct) : Product
    {
        $catalogProduct = $shoppingCartProduct->getCatalogProduct(TRUE);

        $product = new Product();
        $product->setOrderId($order->getId());
        $product->setProductId($shoppingCartProduct->getProductId());
        $product->setExternalSystemId($catalogProduct->getExternalSystemId());
        $product->setCode($catalogProduct->getCode());
        $product->setName($shoppingCartProduct->getTranslatedName());
        $product->setQuantity($shoppingCartProduct->getQuantity());
        $product->setDiscount($shoppingCartProduct->getDiscount());
        $product->setVat($catalogProduct->getVat());
        $product->setUnitPrice($shoppingCartProduct->getUnitPrice());
        $product->setUnitPriceWithoutVat($shoppingCartProduct->getUnitPriceWithoutVat());
        $product->setUnitPriceBeforeDiscount($shoppingCartProduct->getUnitPriceBeforeDiscount());
        $product->setUnitPriceBeforeDiscountWithoutVat($shoppingCartProduct->getUnitPriceBeforeDiscountWithoutVat());
        $product->setSummaryPrice($shoppingCartProduct->getSummaryPrice());
        $product->setSummaryPriceWithoutVat($shoppingCartProduct->getSummaryPriceWithoutVat());
        $product->setSummaryPriceBeforeDiscount($shoppingCartProduct->getSummaryPriceBeforeDiscount());
        $product->setSummaryPriceBeforeDiscountWithoutVat($shoppingCartProduct->getSummaryPriceBeforeDiscountWithoutVat());
        $product->setSurchargePercent($shoppingCartProduct->getSurchargePercent());
        $product->setSurcharge($shoppingCartProduct->getSurcharge());
        $product->setSurchargeWithoutVat($shoppingCartProduct->getSurchargeWithoutVat());
        $product->setInStock($catalogProduct->isInStock());

        $productionTime = $shoppingCartProduct->getProductionTime();
        if ($productionTime) {
        	$product->setProductionTimeId($productionTime->getId());
        	$product->setProductionTimeName($productionTime->getTranslation()->getName());
		}

        return $product;
    }
}
<?php

declare(strict_types = 1);

namespace App\Product;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductFactory extends NObject
{


    /**
     * @param string $code
     * @param string|NULL $photo
     * @param int $stockState
     * @param int $emptyStockState
     * @param int $stock
     * @param float $price
     * @param float $vat
     * @param string $state
     * @param \DateTime|null $newUntilTo
     * @param \DateTime|null $limitedUntilTo
     * @param \DateTime|null $bestsellerUntilTo
     * @param \DateTime|null $goodpriceUntilTo
     * @param \DateTime|null $rareUntilTo
     * @param $saleOnline bool
	 * @param $googleMerchantCategory string|null
	 * @param $googleMerchantBrand int|null
     * @return Product
     */
    public function create(string $code,
                           string $photo = NULL,
                           int $stockState,
                           int $emptyStockState,
                           int $stock,
                           float $price,
                           float $vat,
                           string $state,
                           \DateTime $newUntilTo = NULL,
                           \DateTime $limitedUntilTo = NULL,
                           \DateTime $bestsellerUntilTo = NULL,
                           \DateTime $goodpriceUntilTo = NULL,
                           \DateTime $rareUntilTo = NULL,
                           bool $saleOnline,
                           string $googleMerchantCategory = NULL,
                           int $googleMerchantBrand = NULL) : Product
    {
        $product = new Product();
        $product->setCode($code);
        $product->setPhoto($photo);
        $product->setStockState($stockState);
        $product->setEmptyStockState($emptyStockState);
        $product->setStock($stock);
        $product->setPrice($price);
        $product->setVat($vat);
        $product->setState($state);
        $product->setAddDate(new \DateTime());
        $product->setNewUntilTo($newUntilTo ? $newUntilTo->format('Y-m-d') : NULL);
        $product->setLimitedUntilTo($limitedUntilTo ? $limitedUntilTo->format('Y-m-d') : NULL);
        $product->setBestsellerUntilTo($bestsellerUntilTo ? $bestsellerUntilTo->format('Y-m-d') : NULL);
        $product->setGoodpriceUntilTo($goodpriceUntilTo ? $goodpriceUntilTo->format('Y-m-d') : NULL);
        $product->setRareUntilTo($rareUntilTo ? $rareUntilTo->format('Y-m-d') : NULL);
        $product->setSaleOnline($saleOnline);
        $product->setGoogleMerchantCategory($googleMerchantCategory);
        $product->setGoogleMerchantBrand($googleMerchantBrand);

        return $product;
    }
}
<?php

declare(strict_types = 1);

namespace App\ShoppingCart;

use App\Product\Product;
use App\Product\ProductDTO;
use App\ShoppingCart\Delivery\ShoppingCartDelivery;
use App\ShoppingCart\Payment\ShoppingCartPayment;
use App\ShoppingCart\Price\Price;
use App\ShoppingCart\Product\ShoppingCartProduct;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 * todo add customer entity ?
 */
class ShoppingCartDTO
{


    /** @var ShoppingCartDelivery|null */
    protected $delivery;

    /** @var ShoppingCart */
    protected $entity;

    /** @var ShoppingCartPayment|null */
    protected $payment;

    /** @var Price */
    protected $price;

    /** @var ShoppingCartProduct[]|array */
    protected $products = [];

    /** @var ProductDTO[]|array */
    protected $catalogProductDTO = [];

    /** @var int summary quantity of products */
    protected $quantity = 0;



    public function __construct(ShoppingCart $shoppingCart)
    {
        $this->entity = $shoppingCart;
    }



    /**
     * @param $shoppingCartProduct ShoppingCartProduct
     * @param $catalogProduct ProductDTO|null
     * @return self
     */
    public function addProduct(ShoppingCartProduct $shoppingCartProduct, ProductDTO $catalogProduct = NULL) : self
    {
        $this->quantity += $shoppingCartProduct->getQuantity();
        $shoppingCartProduct->getProductId() ? $this->products[$shoppingCartProduct->getProductId()] = $shoppingCartProduct : $this->products[] = $shoppingCartProduct;
        if ($catalogProduct !== NULL) {
            $this->catalogProductDTO[$catalogProduct->getProduct()->getId()] = $catalogProduct;
        }
        return $this;
    }



    /**
     * @return ShoppingCart
     */
    public function getEntity() : ShoppingCart
    {
        return $this->entity;
    }



    /**
     * @return ShoppingCartDelivery|null
     */
    public function getDelivery()
    {
        return $this->delivery;
    }



    /**
     * @param ShoppingCartDelivery|null $delivery
     */
    public function setDelivery(ShoppingCartDelivery $delivery)
    {
        $this->delivery = $delivery;
    }



    /**
     * @return ShoppingCartPayment|null
     */
    public function getPayment()
    {
        return $this->payment;
    }



    /**
     * @param ShoppingCartPayment|null $payment
     */
    public function setPayment(ShoppingCartPayment $payment)
    {
        $this->payment = $payment;
    }



    /**
     * @param $price Price
     * @return self
     */
    public function setPrice(Price $price)
    {
        $this->price = $price;
        return $this;
    }



    /**
     * @return Price
     */
    public function getPrice() : Price
    {
        return $this->price;
    }



    /**
     * @return ShoppingCartProduct[]|array
     */
    public function getProducts() : array
    {
        return $this->products;
    }



    /**
     * @param $id int
     * @return ProductDTO|null
     */
    public function getProductDTOByProductId(int $id)
    {
        return $this->catalogProductDTO[$id] ?? NULL;
    }



    /**
     * @return int
     */
    public function countProducts() : int
    {
        return count($this->products);
    }



    /**
     * @return int
     */
    public function getQuantity() : int
    {
        return $this->quantity;
    }



    /**
     * @return bool
     */
    public function hasProducts() : bool
    {
        return count($this->getProducts()) !== 0;
    }



    /**
     * @return bool
     */
    public function hasAvailableProduct() : bool
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            if ($product->getCatalogProduct()) {
                return TRUE;
            }
        }
        return FALSE;
    }



    /**
     * @return bool
     */
    public function hasProductWithDiscountAllowed() : bool
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            /** @var $catalogProduct Product */
            $catalogProduct = $product->getCatalogProduct();
            if ($catalogProduct && $catalogProduct->isDiscountAllowed()) {
                return TRUE;
            }
        }
        return FALSE;
    }



    /**
     * @return bool
     */
    public function hasNonStockableProducibleProduct() : bool
    {
        $products = $this->getProducts();
        foreach ($products as $product) {
            $catalogProduct = $product->getCatalogProduct();
            if ($catalogProduct && $catalogProduct->isInStock() !== TRUE) {
                $productDTO = $this->getProductDTOByProductId($catalogProduct->getId());
                if ($productDTO->getState()->isProduction()) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
}
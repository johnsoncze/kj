<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Product;

use App\AddDateTrait;
use App\BaseEntity;
use App\Helpers\Prices;
use App\Product\Product;
use App\Product\Production\Time\Time;
use App\ShoppingCart\IShoppingCartPrice;
use App\ShoppingCart\PriceTrait;
use App\ShoppingCart\ShoppingCartIdTrait;
use Kdyby\Translation\ITranslator;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\EntityException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="shopping_cart_product")
 *
 * @method setName($name)
 * @method getName()
 * @method setProductId($id)
 * @method getProductId()
 * @method getQuantity()
 * @method setProductionTimeId($id)
 * @method getProductionTimeId()
 */
class ShoppingCartProduct extends BaseEntity implements IEntity, IShoppingCartPrice
{


    /** @var string */
    const QUANTITY_INCREASE = 'increase';
    const QUANTITY_DECREASE = 'decrease';

    use AddDateTrait;
    use PriceTrait;
    use ShoppingCartIdTrait;

    /**
     * @Column(name="scp_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="scp_name")
     */
    protected $name;

    /**
     * @Column(name="scp_shopping_cart_id")
     */
    protected $shoppingCartId;

    /**
     * @Column(name="scp_product_id")
     */
    protected $productId;

    /**
     * @Column(name="scp_quantity")
     */
    protected $quantity;

    /**
     * @Column(name="scp_discount")
     */
    protected $discount;

    /**
     * @Column(name="scp_price")
     */
    protected $price;

    /**
     * @Column(name="scp_vat")
     */
    protected $vat;

    /**
     * @Column(name="scp_hash")
     */
    protected $hash;

    /**
     * @Column(name="scp_add_date")
     */
    protected $addDate;

    /**
	 * @Column(name="scp_product_production_time_id")
    */
    protected $productionTimeId;

	/**
	 * @var Time|null
	 * @OneToOne(entity="\App\Product\Production\Time\Time")
	 */
	protected $productionTime;

    /**
     * @var Product|null
     * @OneToOne(entity="\App\Product\Product")
     */
    protected $catalogProduct;



    /**
     * @param string $hash
     * @throws EntityException
     */
    public function setHash(string $hash)
    {
        if ($this->hash !== NULL) {
            throw new EntityException('You can not change hash.');
        }
        $this->hash = $hash;
    }



    /**
     * @return string|null
     */
    public function getHash()
    {
        return $this->hash;
    }



    /**
     * @param $quantity int
     * @param $translator ITranslator|null
     * @return self
     * @throws WrongQuantityException invalid quantity
     */
    public function setQuantity(int $quantity, ITranslator $translator = NULL)
    {
        if ($quantity <= 0) {
            $message = $translator ? $translator->translate('shopping-cart.product.wrong.quantity') : 'Quantity can not be less or equal than 0.';
            throw new WrongQuantityException($message);
        }
        $this->quantity = $quantity;
        return $this;
    }



    /**
	 * @param $time Time
	 * @return self
    */
	public function setProductionTime(Time $time) : self
	{
		$this->productionTime = $time;
		return $this;
	}



	/**
	 * @return Time|null
	*/
	public function getProductionTime()
	{
		if ($this->productionTime && $this->productionTime->getState() === Time::PUBLISH) {
			return $this->productionTime;
		}
		return NULL;
	}



	/**
	 * @return void
	*/
	public function removeProductionTime()
	{
		$this->setProductionTimeId(NULL);
		$this->productionTime = NULL;
	}



    /**
     * @param $product Product
     * @return self
     */
    public function setCatalogProduct(Product $product)
    {
        $this->catalogProduct = $product;
        return $this;
    }



    /**
     * @param $required bool
     * @return \App\Product\Product|null
     * @throws \InvalidArgumentException if product is required and missing
     */
    public function getCatalogProduct(bool $required = FALSE)
    {
        if ($this->catalogProduct instanceof \App\Product\Product && $this->catalogProduct->getState() === \App\Product\Product::PUBLISH) {
            return $this->catalogProduct;
        }
        if ($required === TRUE) {
            throw new \InvalidArgumentException('Missing catalog product.');
        }
        return NULL;
    }



    /**
     * Get translated name.
     * @return string|null
     */
    public function getTranslatedName()
    {
        $catalogProduct = $this->getCatalogProduct();
        return $catalogProduct ? $catalogProduct->getTranslation()->getName() : $this->getName();
    }



    /**
     * @return float
     */
    public function getUnitPrice() : float
    {
        return Prices::subtractPercent($this->getPrice(), $this->getDiscount());
    }



    /**
	 * @return float
    */
    public function getUnitPriceWithSurcharge() : float
	{
		return $this->getUnitPrice() + $this->getSurcharge() / $this->getQuantity();
	}



	/**
	 * @return float
	*/
	public function getUnitPriceBeforeDiscountWithSurcharge() : float
	{
		return $this->getUnitPriceBeforeDiscount() + $this->getSurchargeBeforeDiscount() / $this->getQuantity();
	}



    /**
     * @return float
     */
    public function getUnitPriceWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getUnitPrice(), $this->getVat());
    }



    /**
     * @return float
     */
    public function getUnitPriceBeforeDiscount() : float
    {
        return Prices::toBeforeDiscount($this->getUnitPrice(), $this->getDiscount());
    }



    /**
     * @return float
     */
    public function getUnitPriceBeforeDiscountWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getUnitPriceBeforeDiscount(), $this->getVat());
    }



    /**
     * @return float
     */
    public function getSummaryPrice() : float
    {
        return $this->getUnitPrice() * $this->getQuantity() + $this->getSurcharge();
    }



    /**
     * @return float
     */
    public function getSummaryPriceWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getSummaryPrice(), $this->getVat());
    }



    /**
     * @return float
     */
    public function getSummaryPriceBeforeDiscount() : float
    {
        return Prices::toBeforeDiscount($this->getSummaryPrice(), $this->getDiscount());
    }



    /**
     * @return float
     */
    public function getSummaryPriceBeforeDiscountWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getSummaryPriceBeforeDiscount(), $this->getVat());
    }



    /**
     * @return float
     */
    public function getSurcharge() : float
    {
    	$price = 0.0;
    	$percent = $this->getSurchargePercent();
    	if (!empty($percent)) {
			$summaryPrice = $this->getUnitPrice() * $this->getQuantity();
			$price = Prices::addPercent($summaryPrice, $percent) - $summaryPrice;
		}
		return $price;
    }



    /**
	 * @return float
    */
	public function getSurchargeBeforeDiscount() : float
	{
		$price = 0.0;
		$percent = $this->getSurchargePercent();
		if (!empty($percent)) {
			$summaryPrice = $this->getUnitPriceBeforeDiscount() * $this->getQuantity();
			$price = Prices::addPercent($summaryPrice, $percent) - $summaryPrice;
		}
		return $price;
	}



    /**
     * @return float
     */
    public function getSurchargeWithoutVat() : float
    {
        return Prices::toBeforePercent($this->getSurcharge(), $this->getVat());
    }



    /**
     * @return float
     */
    public function getSurchargePercent() : float
    {
        $percent = 0.0;
        $productionTime = $this->getProductionTime();
        return $productionTime && $productionTime->getSurcharge() ? (float)$productionTime->getSurcharge() : $percent;
    }



    /**
     * @param $type string type of reduce
     * @return self
     * @throws \InvalidArgumentException unknown type of reduce
     * @throws WrongQuantityException
     */
    public function reduceQuantity(string $type) : self
    {
        $quantity = $this->getQuantity();
        if ($type === ShoppingCartProduct::QUANTITY_INCREASE) {
            $this->setQuantity($quantity + 1);
        } elseif ($type === ShoppingCartProduct::QUANTITY_DECREASE) {
            $this->setQuantity($quantity - 1);
        } else {
            throw new \InvalidArgumentException('Unknown type of reduce.');
        }
        return $this;
    }



    /**
     * Add quantity.
     * @param $quantity int
     * @return self
     * @throws WrongQuantityException
     */
    public function addQuantity(int $quantity) : self
    {
        $this->setQuantity($this->getQuantity() + $quantity);
        return $this;
    }



    /**
     * Remove discount from product.
     * @return void
     */
    public function removeDiscount()
    {
        $this->discount = 0;
    }



    /**
	 * @return bool
    */
    public function isInStock() : bool
	{
		$catalogProduct = $this->getCatalogProduct();
		return $catalogProduct && $catalogProduct->isInStock();
	}
}
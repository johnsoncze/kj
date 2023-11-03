<?php

declare(strict_types = 1);

namespace App\Order\Product;

use App\Order\Price\AbstractPriceEntity;
use App\Price\IPrice;
use App\Product\Production\Time\Time;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="order_product")
 *
 * @method setOrderId($id)
 * @method getOrderId()
 * @method setProductId($id)
 * @method getProductId()
 * @method setExternalSystemId($id)
 * @method getExternalSystemId()
 * @method setCode($code)
 * @method getCode()
 * @method setName($name)
 * @method getName()
 * @method getQuantity()
 * @method getDiscount()
 * @method setUnitPrice($price)
 * @method getUnitPrice()
 * @method setUnitPriceWithoutVat($price)
 * @method getUnitPriceWithoutVat()
 * @method setUnitPriceBeforeDiscount($price)
 * @method getUnitPriceBeforeDiscount()
 * @method setUnitPriceBeforeDiscountWithoutVat($price)
 * @method getUnitPriceBeforeDiscountWithoutVat()
 * @method setVat($vat)
 * @method getVat()
 * @method setSurchargePercent($percent)
 * @method getSurchargePercent()
 * @method setSurcharge($surcharge)
 * @method getSurcharge()
 * @method setSurchargeWithoutVat($surcharge)
 * @method getSurchargeWithoutVat()
 * @method setInStock($stock)
 * @method getInStock()
 * @method setCatalogProduct($product)
 * @method setParameters($parameters)
 * @method getParameters()
 * @method setProductionTimeId($id)
 * @method getProductionTimeId()
 * @method setProductionTimeName($name)
 * @method getProductionTimeName()
 */
class Product extends AbstractPriceEntity implements IPrice, IEntity
{


    /**
     * @var int|null
     * @Column(name="op_id", key="Primary")
     */
    protected $id;

    /**
     * @var int
     * @Column(name="op_order_id")
     */
    protected $orderId;

    /**
     * @var int|null
     * @Column(name="op_product_id")
     */
    protected $productId;

    /**
     * @var int|null
     * @Column(name="op_external_system_id")
     */
    protected $externalSystemId;

    /**
     * @var string
     * @Column(name="op_code")
     */
    protected $code;

    /**
     * @var string
     * @Column(name="op_name")
     */
    protected $name;

    /**
     * @var int
     * @Column(name="op_quantity")
     */
    protected $quantity;

    /**
     * @var float
     * @Column(name="op_discount")
     */
    protected $discount;

    /**
     * @var float
     * @Column(name="op_unit_price")
     */
    protected $unitPrice;

    /**
     * @var float
     * @Column(name="op_unit_price_without_vat")
     */
    protected $unitPriceWithoutVat;

    /**
     * @var float
     * @Column(name="op_unit_price_before_discount")
     */
    protected $unitPriceBeforeDiscount;

    /**
     * @var float
     * @Column(name="op_unit_price_before_discount_without_vat")
     */
    protected $unitPriceBeforeDiscountWithoutVat;

    /**
     * @var float
     * @Column(name="op_summary_price")
     */
    protected $summaryPrice;

    /**
     * @var float
     * @Column(name="op_summary_price_without_vat")
     */
    protected $summaryPriceWithoutVat;

    /**
     * @var float
     * @Column(name="op_summary_price_before_discount")
     */
    protected $summaryPriceBeforeDiscount;

    /**
     * @var float
     * @Column(name="op_summary_price_before_discount_without_vat")
     */
    protected $summaryPriceBeforeDiscountWithoutVat;

    /**
     * @var float
     * @Column(name="op_vat")
     */
    protected $vat;

    /**
     * @var float
     * @Column(name="op_surcharge_percent")
     */
    protected $surchargePercent;

    /**
     * @var float
     * @Column(name="op_surcharge")
     */
    protected $surcharge;

    /**
     * @var float
     * @Column(name="op_surcharge_without_vat")
     */
    protected $surchargeWithoutVat;

    /**
     * @var int
     * @Column(name="op_in_stock")
     */
    protected $inStock;

    /**
	 * @Column(name="op_product_production_time_id")
    */
    protected $productionTimeId;

    /**
	 * @Column(name="op_product_production_time_name")
    */
    protected $productionTimeName;

    /**
	 * @var Time|null
	 * @OneToOne(entity="\App\Product\Production\Time\Time")
     */
    protected $productionTime;

    /**
     * @var \App\Product\Product|null
     * @OneToOne(entity="\App\Product\Product")
     */
    protected $catalogProduct;

    /**
     * @var \App\Order\Product\Parameter\Parameter[]|array
     * @OneToMany(entity="\App\Order\Product\Parameter\Parameter")
     */
    protected $parameters = [];



    /**
     * Setter for 'quantity' property.
     * @param $quantity int
     * @return self
     * @throws \EntityInvalidArgumentException bad quantity
     */
    public function setQuantity(int $quantity) : self
    {
        if ($quantity < 1) {
            throw new \EntityInvalidArgumentException('Quantity can not be less than 1.');
        }
        $this->quantity = $quantity;
        return $this;
    }



    /**
     * @param $discount string|float|null
     * @throws \InvalidArgumentException
     */
    public function setDiscount($discount)
    {
        if ((float)$discount > 100) {
            throw new \InvalidArgumentException(sprintf('Discount can be max 100 %%. %f given.', $discount));
        }
        $this->discount = $discount;
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
	 * @return string|null
	*/
	public function getTranslatedProductionTimeName()
	{
		$productionTime = $this->getProductionTime();
		return $productionTime ? $productionTime->getTranslation()->getName() : $this->productionTimeName;
	}



    /**
     * Was the product in stock in time of order?
     * @return bool
     */
    public function wasInStock() : bool
    {
        return (bool)$this->getInStock();
    }


    /**
     * @return \App\Product\Product|null
     */
    public function getCatalogProduct(bool $allowNotPublished = false)
    {
        if ($this->catalogProduct instanceof \App\Product\Product && ($this->catalogProduct->getState() === \App\Product\Product::PUBLISH || $allowNotPublished)) {
            return $this->catalogProduct;
        }
        return null;
    }



    /**
     * Get translated name from catalog product if exists.
     * @return string
     */
    public function getTranslatedName() : string
    {
        //todo když nastavený jazyk není ten, ve kterém se objednávalo, tak když existuje překlad, zobrazit název z katalogového produktu
        //todo jinak zobrazit název v čase objednávky
        return $this->getName();
    }



    /**
     * @return string|null
    */
    public function getPhoto()
    {
        $catalogProduct = $this->getCatalogProduct();
        return $catalogProduct instanceof \App\Product\Product && $catalogProduct->getPhoto() ? $catalogProduct->getPhoto() : NULL;
    }
}
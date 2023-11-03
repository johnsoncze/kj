<?php

declare(strict_types = 1);

namespace App\Opportunity\Product;

use App\AddDateTrait;
use App\BaseEntity;
use App\Product\Production\ProductionTimeDTO;
use App\Product\Production\ProductionTrait;
use App\ShoppingCart\PriceTrait;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="opportunity_product")
 *
 * @method setOpportunityId($id)
 * @method getOpportunityId()
 * @method setProductId($id)
 * @method getProductId()
 * @method setExternalSystemId($id)
 * @method getExternalSystemId()
 * @method setName($name)
 * @method getName()
 * @method setCode($code)
 * @method getCode()
 * @method setUrl($url)
 * @method getUrl()
 * @method setQuantity($quantity)
 * @method getQuantity()
 * @method setStock($stock)
 * @method getStock()
 * @method setProductionTime($time)
 * @method getProductionTime()
 * @method setProductionTimePercent($percent)
 * @method getProductionTimePercent()
 * @method setComment($comment)
 * @method getComment()
 * @method setParameters($parameters)
 * @method getParameters()
 */
class Product extends BaseEntity implements IEntity
{


    use AddDateTrait;
    use PriceTrait;

    /**
     * @var int|null
     * @Column(name="oppp_id", key="Primary")
     */
    protected $id;

    /**
     * @var int
     * @Column(name="oppp_opportunity_id")
     */
    protected $opportunityId;

    /**
     * @var int|null
     * @Column(name="oppp_product_id")
     */
    protected $productId;

	/**
	 * @var \App\Product\Product|null
	 * @OneToOne(entity="\App\Product\Product")
	 */
	protected $catalogProduct;

    /**
     * @var int|null
     * @Column(name="oppp_external_system_id")
     */
    protected $externalSystemId;

    /**
     * @var string
     * @Column(name="oppp_name")
     */
    protected $name;

    /**
     * @var string
     * @Column(name="oppp_code")
     */
    protected $code;

    /**
     * @var string
     * @Column(name="oppp_url")
     */
    protected $url;

	/**
	 * @var int
	 * @Column(name="oppp_quantity")
	 */
	protected $quantity;

    /**
     * @var float
     * @Column(name="oppp_price")
     */
    protected $price;

    /**
     * @var float
     * @Column(name="oppp_vat")
     */
    protected $vat;

    /**
     * @var float
     * @Column(name="oppp_discount")
    */
    protected $discount;

    /**
     * @var int
     * @Column(name="oppp_was_in_stock")
     */
    protected $stock;

    /**
     * @var string|null
     * @Column(name="oppp_production_time")
    */
    protected $productionTime;

    /**
     * @var float|null
     * @Column(name="oppp_production_time_percent")
    */
    protected $productionTimePercent;

    /**
     * @var string|null
     * @Column(name="oppp_comment")
    */
    protected $comment;

	/**
	 * @var \App\Opportunity\Product\Parameter\Parameter[]|array
	 * @OneToMany(entity="\App\Opportunity\Product\Parameter\Parameter")
	 */
	protected $parameters = [];

    /**
     * @var string|null
     * @Column(name="oppp_add_date")
     */
    protected $addDate;



    /**
     * Was the product in stock in the time of demand?
     * @return bool
     */
    public function wasInStock() : bool
    {
        return (bool)$this->getStock();
    }



    /**
	 * @return float
    */
    public function getSummaryPrice()
	{
		return (float)($this->getPrice() * $this->getQuantity());
	}



	/**
	 * @param $product \App\Product\Product
	 * @return self
	*/
	public function setCatalogProduct(\App\Product\Product $product) : self
	{
		$this->catalogProduct = $product;
		return $this;
	}



	/**
	 * @return \App\Product\Product|null
	*/
	public function getCatalogProduct()
	{
		if ($this->catalogProduct instanceof \App\Product\Product && $this->catalogProduct->getState() === \App\Product\Product::PUBLISH) {
			return $this->catalogProduct;
		}
		return NULL;
	}



	/**
	 * @return string
	*/
	public function getTranslatedName() : string
	{
		$catalogProduct = $this->getCatalogProduct();
		return $catalogProduct ? $catalogProduct->getTranslation()->getName() : $this->getName();
	}



	/**
	 * @return ProductionTimeDTO|null
	 */
	public function getProductionTimeDTO()
	{
		$productionTime = $this->getProductionTime();
		return $productionTime ? ProductionTrait::getProductionTimes()[$productionTime] : NULL;
	}
}
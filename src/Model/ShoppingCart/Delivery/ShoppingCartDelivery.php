<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

use App\AddDateTrait;
use App\BaseEntity;
use App\Delivery\Delivery;
use App\ShoppingCart\IShoppingCartPrice;
use App\ShoppingCart\PriceTrait;
use App\ShoppingCart\ShoppingCartIdTrait;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="shopping_cart_delivery")
 *
 * @method setName($name)
 * @method getName()
 * @method setDeliveryId($id)
 * @method getDeliveryId()
 */
class ShoppingCartDelivery extends BaseEntity implements IEntity, IShoppingCartPrice
{


    use AddDateTrait;
    use PriceTrait;
    use ShoppingCartIdTrait;


    /**
     * @Column(name="scd_id", key="Primary")
     */
    protected $id;

    /**
     * @var string|null
     * @Column(name="scd_name")
     */
    protected $name;

    /**
     * @Column(name="scd_shopping_cart_id")
     */
    protected $shoppingCartId;

    /**
     * @Column(name="scd_delivery_id")
     */
    protected $deliveryId;

    /**
     * @Column(name="scd_discount")
     */
    protected $discount;

    /**
     * @Column(name="scd_price")
     */
    protected $price;

    /**
     * @Column(name="scd_vat")
     */
    protected $vat;

    /**
     * @Column(name="scd_add_date")
     */
    protected $addDate;

    /**
     * @var Delivery|null
     * @OneToOne(entity="\App\Delivery\Delivery")
     */
    protected $catalogDelivery;



    /**
     * @param $delivery Delivery
     * @return self
     */
    public function setCatalogDelivery(Delivery $delivery) : self
    {
        $this->catalogDelivery = $delivery;
        return $this;
    }



    /**
     * @return Delivery|null
     */
    public function getCatalogDelivery()
    {
        if ($this->catalogDelivery instanceof Delivery && $this->catalogDelivery->getState() === Delivery::ALLOWED) {
            return $this->catalogDelivery;
        }
        return NULL;
    }



    /**
     * @return string
     */
    public function getTranslatedName() : string
    {
        $catalogDelivery = $this->getCatalogDelivery();
        return $catalogDelivery ? $catalogDelivery->getTranslation()->getName() : $this->getName();
    }
}
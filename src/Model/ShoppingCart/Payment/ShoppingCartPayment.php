<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Payment;

use App\AddDateTrait;
use App\BaseEntity;
use App\Payment\Payment;
use App\ShoppingCart\IShoppingCartPrice;
use App\ShoppingCart\PriceTrait;
use App\ShoppingCart\ShoppingCartIdTrait;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="shopping_cart_payment")
 *
 * @method setName($name)
 * @method getName()
 * @method setPaymentId(int $id)
 * @method getPaymentId()
 */
class ShoppingCartPayment extends BaseEntity implements IEntity, IShoppingCartPrice
{


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
     * @Column(name="scp_payment_id")
     */
    protected $paymentId;

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
     * @Column(name="scp_add_date")
     */
    protected $addDate;

    /**
     * @var Payment|null
     * @OneToOne(entity="\App\Payment\Payment")
     */
    protected $catalogPayment;



    /**
     * @param $payment Payment
     * @return self
     */
    public function setCatalogPayment(Payment $payment) : self
    {
        $this->catalogPayment = $payment;
        return $this;
    }



    /**
     * @return Payment|null
     */
    public function getCatalogPayment()
    {
        if ($this->catalogPayment instanceof Payment && $this->catalogPayment->getState() === Payment::ALLOWED) {
            return $this->catalogPayment;
        }
        return NULL;
    }



    /**
     * @return string
     */
    public function getTranslatedName() : string
    {
        $payment = $this->getCatalogPayment();
        return $payment ? $payment->getTranslation()->getName() : $this->getName();
    }
}
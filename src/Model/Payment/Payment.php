<?php

declare(strict_types = 1);

namespace App\Payment;

use App\AddDateTrait;
use App\BaseEntity;
use App\EntitySortTrait;
use App\StateTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Entities\Interfaces\IAllow;
use Ricaefeliz\Mappero\Entities\Traits\AllowTrait;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="payment")
 *
 * @method setExternalSystemId($id)
 * @method getExternalSystemId()
 * @method setCreditCard($bool)
 * @method getCreditCard()
 * @method setTransfer($bool)
 * @method getTransfer()
 * @method setPrice($price)
 * @method getPrice()
 * @method setVat($vat)
 * @method getVat()
 * @method setNonStockProducibleProductAvailability($bool)
 * @method getNonStockProducibleProductAvailability()
 */
class Payment extends BaseEntity implements IEntity, IAllow, ITranslatable
{


    use AddDateTrait;
    use AllowTrait;
    use EntitySortTrait;
    use StateTrait;
    use TranslationTrait;


    /**
     * @Column(name="py_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="py_external_system_id")
     */
    protected $externalSystemId;

    /**
     * @Translation
     * @OneToMany(entity="\App\Payment\Translation\PaymentTranslation")
     */
    protected $translations;

    /**
     * @Translation
     * @ManyToMany(entity="\App\Delivery\Delivery")
     */
    protected $deliveries;

    /**
     * @Column(name="py_credit_card")
     */
    protected $creditCard;

    /**
     * @Column(name="py_transfer")
     */
    protected $transfer;

    /**
     * @Column(name="py_price")
     */
    protected $price;

    /**
     * @Column(name="py_vat")
     */
    protected $vat;

    /**
     * @Column(name="py_sort")
     */
    protected $sort;

    /**
     * @Column(name="py_non_stock_producible_product_availability")
     */
    protected $nonStockProducibleProductAvailability;

    /**
     * @Column(name="py_state")
     */
    protected $state;

    /**
     * @Column(name="py_add_date")
     */
    protected $addDate;



    /**
     * @return bool
     */
    public function isRequiredPaymentGateway() : bool
    {
        return (bool)$this->getCreditCard();
    }



    /**
     * @return bool
     */
    public function isTransfer() : bool
    {
        return (bool)$this->getTransfer();
    }



    /**
     * @return bool
     */
    public function isAvailableForNonStockProducibleProduct() : bool
    {
        return (bool)$this->getNonStockProducibleProductAvailability();
    }

    /**
     * @return mixed
     */
    public function getDeliveries() {
        return $this->deliveries;
    }

    /**
     * @param mixed $deliveries
     */
    public function setDeliveries($deliveries) {
        $this->deliveries = $deliveries;
    }
}
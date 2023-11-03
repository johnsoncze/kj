<?php

declare(strict_types = 1);

namespace App\Delivery;

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
 * @Table(name="delivery")
 *
 * @method setExternalSystemId($id)
 * @method getExternalSystemId()
 * @method setPrice($price)
 * @method getPrice()
 * @method setVat($vat)
 * @method getVat()
 */
class Delivery extends BaseEntity implements IEntity, ITranslatable, IAllow
{


	use AddDateTrait;
	use AllowTrait;
	use EntitySortTrait;
	use StateTrait;
	use TranslationTrait;


	/**
	 * @Column(name="d_id", key="Primary")
	 */
	protected $id;

	/**
	 * @var int
	 * @Column(name="d_external_system_id")
	 */
	protected $externalSystemId;

	/**
	 * @Translation
	 * @OneToMany(entity="\App\Delivery\Translation\DeliveryTranslation")
	 */
	protected $translations;

	/**
	 * @Translation
	 * @ManyToMany(entity="\App\Payment\Payment")
	 */
	protected $payments;

	/**
	 * @Column(name="d_price")
	 */
	protected $price;

	/**
	 * @Column(name="d_vat")
	 */
	protected $vat;

	/**
	 * @Column(name="d_sort")
	 */
	protected $sort;

	/**
	 * @Column(name="d_state")
	 */
	protected $state;

	/**
	 * @Column(name="d_add_date")
	 */
	protected $addDate;



	/**
	 * @return bool
	 */
	public function isStore() : bool
	{
		return $this->getId() === 5;
	}

    /**
     * @return mixed
     */
    public function getPayments() {
        return $this->payments;
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments) {
        $this->payments = $payments;
    }
}
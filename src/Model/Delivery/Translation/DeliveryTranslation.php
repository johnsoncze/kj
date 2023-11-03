<?php

declare(strict_types = 1);

namespace App\Delivery\Translation;

use App\AddDateTrait;
use App\BaseEntity;
use App\LanguageTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="delivery_translation")
 *
 * @method setDeliveryId($id)
 * @method getDeliveryId()
 * @method setName($name)
 * @method getName()
 */
class DeliveryTranslation extends BaseEntity implements IEntity , ITranslation
{


    use AddDateTrait;
    use LanguageTrait;


    /**
     * @Column(name="dt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="dt_delivery_id")
     */
    protected $deliveryId;

    /**
     * @Column(name="dt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="dt_name")
     */
    protected $name;

    /**
     * @Column(name="dt_add_date")
     */
    protected $addDate;
}
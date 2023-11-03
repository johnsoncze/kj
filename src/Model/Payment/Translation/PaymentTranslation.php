<?php

declare(strict_types = 1);

namespace App\Payment\Translation;

use App\AddDateTrait;
use App\BaseEntity;
use App\LanguageTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="payment_translation")
 *
 * @method setPaymentId($id)
 * @method getPaymentId()
 * @method setLanguageId($id)
 * @method getLanguageId()
 * @method setName($name)
 * @method getName()
 */
class PaymentTranslation extends BaseEntity implements IEntity, ITranslation
{


    use AddDateTrait;
    use LanguageTrait;


    /**
     * @Column(name="pyt_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pyt_payment_id")
     */
    protected $paymentId;

    /**
     * @Column(name="pyt_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="pyt_name")
     */
    protected $name;

    /**
     * @Column(name="pyt_add_date")
     */
    protected $addDate;
}
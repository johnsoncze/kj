<?php

declare(strict_types = 1);

namespace App\ProductState\Translation;

use App\AddDateTrait;
use App\BaseEntity;
use App\LanguageTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_state_translation")
 *
 * @method setStateId($id)
 * @method getStateId()
 * @method setValue($value)
 * @method getValue()
 */
class ProductStateTranslation extends BaseEntity implements IEntity, ITranslation
{


    use AddDateTrait;
    use LanguageTrait;


    /**
     * @Column(name="pst_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pst_state_id")
     */
    protected $stateId;

    /**
     * @Column(name="pst_language_id")
     */
    protected $languageId;

    /**
     * @Column(name="pst_value")
     */
    protected $value;

    /**
     * @Column(name="pst_add_date")
     */
    protected $addDate;

}
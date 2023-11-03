<?php

declare(strict_types = 1);

namespace App\Periskop\WeedingRing\Mapping;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="periskop_weeding_ring_mapping")
 *
 * @method setMaleId($id)
 * @method getMaleId()
 * @method setFemaleId($id)
 * @method getFemaleId()
 * @method setProductId($id)
 * @method getProductId()
 */
class Mapping extends BaseEntity implements IEntity
{


    use AddDateTrait;

    /**
     * @Column(name="pwrm_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="pwrm_male_id")
     */
    protected $maleId;

    /**
     * @Column(name="pwrm_female_id")
     */
    protected $femaleId;

    /**
     * @Column(name="pwrm_product_id")
     */
    protected $productId;

    /**
     * @Column(name="pwrm_add_date")
     */
    protected $addDate;
}
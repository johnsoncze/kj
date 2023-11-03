<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup\Lock\Parameter;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 *  * Ideas:
 * - add language id in case of need
 * - removed value property and create chain of responsibility pattern
 * which will be return result
 *
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_group_lock_parameter")
 *
 * @method setLockId($id)
 * @method getLockId()
 * @method setParameterId($id)
 * @method getParameterId()
 * @method setValue($value)
 * @method getValue()
 * @method setWeight($weight)
 * @method getWeight()
 */
class Parameter extends BaseEntity implements IEntity
{


    /** @var string */
    const QUALITY_VALUE = 'quality_info';
    const RING_SIZE_ADJUSTMENT_VALUE = 'ring_size_adjustment';
    const WARRANTY_JK_VALUE = 'warranty_jk';
    const WEEDING_RING_DISCOUNT  = 'weeding_ring_discount';


    /**
     * @Column(name="ppglp_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ppglp_lock_id")
     */
    protected $lockId;

    /**
     * @Column(name="ppglp_parameter_id")
     */
    protected $parameterId;

    /**
     * @Column(name="ppglp_value")
     */
    protected $value;

    /**
	 * @Column(name="ppglp_weight")
    */
    protected $weight;
}
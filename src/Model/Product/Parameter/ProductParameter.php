<?php

declare(strict_types = 1);

namespace App\Product\Parameter;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_parameter_relationship")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setParameterId($id)
 * @method getParameterId()
 */
class ProductParameter extends BaseEntity implements IEntity
{


    use AddDateTrait;

    /**
     * @Column(name="ppr_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="ppr_product_id")
     */
    protected $productId;

    /**
     * @Column(name="ppr_parameter_id")
     */
    protected $parameterId;

    /**
     * @Column(name="ppr_add_date")
     */
    protected $addDate;
}
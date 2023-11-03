<?php

declare(strict_types = 1);

namespace App\CategoryProductParameter;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_product_parameter")
 *
 * @method setCategoryId($id)
 * @method getCategoryId()
 * @method setProductParameterId($id)
 * @method getProductParameterId()
 */
class CategoryProductParameterEntity extends BaseEntity implements IEntity
{


    /**
     * @Column(name="cpr_id", key="Primary")
     */
    protected $id;


    /**
     * @Column(name="cpr_category_id")
     */
    protected $categoryId;

    /**
     * @Column(name="cpr_product_parameter_id")
     */
    protected $productParameterId;
}
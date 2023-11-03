<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroupParameter;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_filtration_group_parameter")
 * @method setCategoryFiltrationGroupId($id)
 * @method getCategoryFiltrationGroupId()
 * @method setProductParameterId($id)
 * @method getProductParameterId()
 * @method setProductParameter($parameter)
 * @method getProductParameter()
 */
class CategoryFiltrationGroupParameterEntity extends BaseEntity implements IEntity
{


    /**
     * @Column(name="cfgi_id", key="Primary")
     */
    protected $id;

    /**
     * @Column(name="cfgi_category_filtration_group_id")
     */
    protected $categoryFiltrationGroupId;

    /**
     * @Column(name="cfgi_product_parameter_id")
     */
    protected $productParameterId;

    /**
     * @OneToOne(entity="\App\ProductParameter\ProductParameterEntity")
    */
    protected $productParameter;
}
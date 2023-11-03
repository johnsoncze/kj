<?php

declare(strict_types = 1);

namespace App\Product\Variant;

use App\AddDateTrait;
use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_variant")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setProductParameterId($id)
 * @method getProductParameterId()
 * @method setProductParameterRelationId($id)
 * @method getProductParameterRelationId()
 * @method setProductVariantId($id)
 * @method getProductVariantId()
 * @method setProductVariantParameterId($id)
 * @method getProductVariantParameterId()
 * @method setProductVariantParameterRelationId($id)
 * @method getProductVariantParameterRelationId()
 * @method setParameterGroupId($id)
 * @method getParameterGroupId()
 * @method setParentVariantId($id)
 * @method getParentVariantId()
 */
class Variant extends BaseEntity implements IEntity
{


	use AddDateTrait;

	/**
	 * @Column(name="pv_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="pv_product_id")
	 */
	protected $productId;

	/**
	 * @Column(name="pv_product_parameter_id")
	 */
	protected $productParameterId;

	/**
	 * @Column(name="pv_product_parameter_relation_id")
	*/
	protected $productParameterRelationId;

	/**
	 * @Column(name="pv_product_variant_id")
	 */
	protected $productVariantId;

	/**
	 * @Column(name="pv_product_variant_parameter_id")
	*/
	protected $productVariantParameterId;

	/**
	 * @Column(name="pv_product_variant_parameter_relation_id")
	 */
	protected $productVariantParameterRelationId;

	/**
	 * @Column(name="pv_parameter_group_id")
	*/
	protected $parameterGroupId;

	/**
     * @Column(name="pv_parent_variant_id")
	*/
	protected $parentVariantId;

}
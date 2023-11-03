<?php

declare(strict_types = 1);

namespace App\Category\Product\Sorting;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_product_sorting")
 *
 * @method setProductId($id)
 * @method getProductId()
 * @method setCategoryId($id)
 * @method getCategoryId()
 * @method setSorting($sorting)
 * @method getSorting()
 * @method setCreatedDate($date)
 * @method getCreatedDate()
 */
class Sorting extends BaseEntity implements IEntity
{


	/**
	 * @Column(name="cps_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="cps_category_id")
	 */
	protected $categoryId;

	/**
	 * @Column(name="cps_product_id")
	 */
	protected $productId;

	/**
	 * @Column(name="cps_sorting")
	 */
	protected $sorting;

	/**
	 * @Column(name="cps_created_date")
	 */
	protected $createdDate;
}
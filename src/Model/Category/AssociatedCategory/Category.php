<?php

declare(strict_types = 1);

namespace App\Category\AssociatedCategory;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="category_associated_category")
 *
 * @method setCategoryId($id)
 * @method getCategoryId()
 * @method setAssociatedCategoryId($id)
 * @method getAssociatedCategoryId()
 * @method setAddedDate($date)
 * @method getAddedDate()
 */
class Category extends BaseEntity implements IEntity
{


	/**
	 * @Column(name="cac_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="cac_category_id")
	 */
	protected $categoryId;

	/**
	 * @Column(name="cac_associated_category_id")
	 */
	protected $associatedCategoryId;

	/**
	 * @Column(name="cac_added_date")
	 */
	protected $addedDate;
}
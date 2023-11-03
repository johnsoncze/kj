<?php

declare(strict_types = 1);

namespace App\ProductParameter\Helper;

use App\BaseEntity;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="parameter_helper")
 *
 * @method setKey($key)
 * @method getKey()
 * @method setName($name)
 * @method getName()
 * @method setValue($value)
 * @method getValue()
 */
class Helper extends BaseEntity implements IEntity
{


	/** @var string */
	const COLOR_LIST_KEY = ProductParameterGroupEntity::FILTRATION_TYPE_COLOR_LIST;

	/**
	 * @Column(name="pph_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="pph_key")
	 */
	protected $key;

	/**
	 * @Column(name="pph_name")
	 */
	protected $name;

	/**
	 * @Column(name="pph_value")
	 */
	protected $value;
}
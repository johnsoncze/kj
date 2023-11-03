<?php

declare(strict_types = 1);

namespace App\ProductState;

use App\AddDateTrait;
use App\BaseEntity;
use App\EntitySortTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_state")
 *
 * @method setProduction($bool)
 * @method getProduction()
 */
class ProductState extends BaseEntity implements IEntity, ITranslatable
{


	use AddDateTrait;
	use EntitySortTrait;
	use TranslationTrait;

	/**
	 * @Column(name="ps_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="ps_production")
	 */
	protected $production;

	/**
	 * @Translation
	 * @OneToMany(entity="\App\ProductState\Translation\ProductStateTranslation")
	 */
	protected $translations;

	/**
	 * @Column(name="ps_sort")
	 */
	protected $sort;

	/**
	 * @Column(name="ps_add_date")
	 */
	protected $addDate;



	/**
	 * @return bool
	 */
	public function isProduction() : bool
	{
		return (bool)$this->production;
	}
}
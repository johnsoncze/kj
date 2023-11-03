<?php

declare(strict_types = 1);

namespace App\Product\Production\Time;

use App\AddDateTrait;
use App\BaseEntity;
use App\IPublication;
use App\Product\Production\ProductionTimeDTO;
use App\PublicationTrait;
use App\StateTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslatable;
use Ricaefeliz\Mappero\Translation\TranslationTrait;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_production_time")
 *
 * @method setSurcharge($surcharge)
 * @method getSurcharge()
 * @method setDefault($bool)
 * @method getDefault()
 * @method setSort($sort)
 * @method getSort()
 */
class Time extends BaseEntity implements IEntity, ITranslatable, IPublication
{


	use AddDateTrait;
	use PublicationTrait;
	use StateTrait;
	use TranslationTrait;

	/**
	 * @Column(name="ppt_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Translation
	 * @OneToMany(entity="\App\Product\Production\Time\Translation\TimeTranslation")
	 */
	protected $translations;

	/**
	 * @Column(name="ppt_surcharge")
	*/
	protected $surcharge;

	/**
     * @Column(name="ppt_default")
	*/
	protected $default;

	/**
	 * @Column(name="ppt_state")
	 */
	protected $state;

	/**
	 * @Column(name="ppt_sort")
	 */
	protected $sort;

	/**
	 * @Column(name="ppt_add_date")
	 */
	protected $addDate;



	/**
	 * @return string
	*/
	public function toString() : string
	{
		$string = $this->getTranslation()->getName();
		$string .= $this->getSurcharge() ? sprintf(' (+%s %%)', $this->getSurcharge()) : NULL;
		return $string;
	}



	/**
	 * Temporary id resolver.
	 * @param $time string
	 * @return int
	*/
	public static function resolveId(string $time) : int
	{
		return (int)str_replace([ProductionTimeDTO::PRODUCTION_4_6_WEEKS, ProductionTimeDTO::PRODUCTION_24_HOURS], [1, 2], $time);
	}
}
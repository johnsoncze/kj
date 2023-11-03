<?php

declare(strict_types = 1);

namespace App\Product\Production\Time\Translation;

use App\AddDateTrait;
use App\BaseEntity;
use App\LanguageTrait;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Translation\ITranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @Table(name="product_production_time_translation")
 *
 * @method setTimeId($id)
 * @method getTimeId()
 * @method setName($name)
 * @method getName()
 */
class TimeTranslation extends BaseEntity implements IEntity, ITranslation
{


	use AddDateTrait;
	use LanguageTrait;

	/**
	 * @Column(name="pptt_id", key="Primary")
	 */
	protected $id;

	/**
	 * @Column(name="pptt_time_id")
	 */
	protected $timeId;

	/**
	 * @Column(name="pptt_language_id")
	 */
	protected $languageId;

	/**
	 * @Column(name="pptt_name")
	 */
	protected $name;

	/**
	 * @Column(name="pptt_add_date")
	 */
	protected $addDate;
}
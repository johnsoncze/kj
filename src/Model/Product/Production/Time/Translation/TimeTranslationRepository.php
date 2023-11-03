<?php

declare(strict_types = 1);

namespace App\Product\Production\Time\Translation;

use Ricaefeliz\Mappero\Repositories\BaseRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TimeTranslationRepository extends BaseRepository
{


	/** @var string */
	protected $entityName = TimeTranslation::class;
}
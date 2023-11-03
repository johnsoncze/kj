<?php

declare(strict_types = 1);

namespace App\Tests\Product\Production\Time\Translation;

use App\Product\Production\Time\Translation\TimeTranslation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait TimeTranslationTestTrait
{


	/**
	 * @return TimeTranslation
	 */
	private function createTestTimeTranslation() : TimeTranslation
	{
		$translation = new TimeTranslation();
		$translation->setTimeId(1);
		$translation->setLanguageId(1);
		$translation->setName('Standard');

		return $translation;
	}
}
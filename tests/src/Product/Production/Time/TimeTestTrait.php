<?php

declare(strict_types = 1);

namespace App\Tests\Product\Production\Time;

use App\Product\Production\Time\Time;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait TimeTestTrait
{


	/**
	 * @return Time
	 */
	private function createTestTime() : Time
	{
		$time = new Time();
		$time->setSurcharge(50.0);
		$time->setSort(1);
		$time->setState(Time::PUBLISH);

		return $time;
	}
}
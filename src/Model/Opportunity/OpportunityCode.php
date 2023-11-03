<?php

declare(strict_types = 1);

namespace App\Opportunity;

use Nette\Utils\DateTime;
use Nette\Utils\Random;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class OpportunityCode
{


	/**
	 * Generate new code.
	 * @return string
	 * @throws \InvalidArgumentException unknown type
	 */
	public function generate() : string
	{
		$actualDate = new DateTime();
		$number = Random::generate(6, '0-9');

		return 'OP' . $actualDate->format('y') . $number;
	}
}
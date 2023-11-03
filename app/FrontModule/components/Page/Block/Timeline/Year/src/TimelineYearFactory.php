<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Block\Timeline\Year;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface TimelineYearFactory
{


	/**
	 * @return TimelineYear
	 */
	public function create();
}
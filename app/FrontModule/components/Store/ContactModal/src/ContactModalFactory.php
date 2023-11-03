<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Store\ContactModal;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ContactModalFactory
{


	/**
	 * @return ContactModal
	 */
	public function create();
}
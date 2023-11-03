<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Breadcrumb;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface BreadcrumbFactory
{


	/**
	 * @return Breadcrumb
	 */
	public function create();
}
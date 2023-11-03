<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Category\Articles;


interface ArticlesFactory
{


	/**
	 * @return Articles
	 */
	public function create();
}
<?php

declare(strict_types = 1);

namespace App\Product\Photo;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IPhoto
{


	/**
	 * @return string|null
	 */
	public function getPhotoName();



	/**
	 * @return string
	 */
	public function getUploadFolder() : string;
}
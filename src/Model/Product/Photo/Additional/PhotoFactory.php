<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

use App\Product\Product;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PhotoFactory
{


	/**
	 * @param $product Product
	 * @param $name string
	 * @return ProductAdditionalPhoto
	 */
	public function create(Product $product, string $name) : ProductAdditionalPhoto
	{
		$photo = new ProductAdditionalPhoto();
		$photo->setProductId($product->getId());
		$photo->setFileName($name);
        $photo->setAddDate(new \DateTime());

		return $photo;
	}
}
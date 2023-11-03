<?php

declare(strict_types = 1);

namespace App\Product\Variant;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class VariantDuplication
{


	/**
	 * Check if exist a variant already.
	 * @param $variant Variant
	 * @param $variantRepository VariantRepository
	 * @return Variant
	 * @throws VariantDuplicationException
	 */
	public function check(Variant $variant, VariantRepository $variantRepository) : Variant
	{
		$duplicateVariant = $variantRepository->findOneByProductIdAndProductVariantIdAndProductVariantParameterIdAndParentVariantId($variant->getProductId(), $variant->getProductVariantId(), $variant->getProductVariantParameterId(), $variant->getParentVariantId());
		if ($duplicateVariant !== NULL) {
			throw new VariantDuplicationException('Varianta již existuje.');
		}
		return $variant;
	}
}
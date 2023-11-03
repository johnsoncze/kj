<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationCheckDuplicate extends NObject
{


    /**
     * @param ProductParameterEntity $parameter
     * @param ProductParameterTranslationEntity $translation
     * @param ProductParameterEntity|NULL $duplicateParameter
     * @param ProductParameterTranslationEntity|NULL $duplicateTranslation
     * @return ProductParameterTranslationEntity
     * @throws ProductParameterTranslationCheckDuplicateException
     */
    public function check(ProductParameterEntity $parameter,
                          ProductParameterTranslationEntity $translation,
                          ProductParameterEntity $duplicateParameter = NULL,
                          ProductParameterTranslationEntity $duplicateTranslation = NULL)
    : ProductParameterTranslationEntity
    {
        if ($duplicateParameter
            && $duplicateTranslation
            && (int)$parameter->getProductParameterGroupId() === (int)$duplicateParameter->getProductParameterGroupId()
            && (int)$translation->getLanguageId() === (int)$duplicateTranslation->getLanguageId()
            && $translation->getValue() === $duplicateTranslation->getValue()
            && $translation->getId() !== $duplicateTranslation->getId()
        ) {
            throw new ProductParameterTranslationCheckDuplicateException("Parametr '{$duplicateTranslation->getValue()}' již existuje.");
        }

        return $translation;
    }
}
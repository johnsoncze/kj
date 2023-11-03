<?php

declare(strict_types = 1);

namespace App\ProductParameter;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationEntityFactory extends NObject
{


    /**
     * @param int $productParameterId
     * @param int $languageId
     * @param string $value
     * @param $url string|null
     * @return ProductParameterTranslationEntity
     */
    public function create(int $productParameterId, int $languageId, string $value, string $url = NULL) : ProductParameterTranslationEntity
    {
        $entity = new ProductParameterTranslationEntity();
        $entity->setProductParameterId($productParameterId);
        $entity->setLanguageId($languageId);
        $entity->setUrl($url);
        $entity->setValue($value);

        return $entity;
    }
}
<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationEntityFactory extends Control
{


    /**
     * @param int $productParameterGroupId
     * @param int $languageId
     * @param string $name
     * @param string $filtrationTitle
     * @return ProductParameterGroupTranslationEntity
     */
    public function create(int $productParameterGroupId,
                           int $languageId,
                           string $name,
                           string $filtrationTitle) : ProductParameterGroupTranslationEntity
    {
        $entity = new ProductParameterGroupTranslationEntity();
        $entity->setProductParameterGroupId($productParameterGroupId);
        $entity->setLanguageId($languageId);
        $entity->setName($name);
        $entity->setFiltrationTitle($filtrationTitle);

        return $entity;
    }
}
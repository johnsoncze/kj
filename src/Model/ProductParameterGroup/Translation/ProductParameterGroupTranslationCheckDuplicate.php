<?php

declare(strict_types = 1);

namespace App\ProductParameterGroup;

use Nette\Application\UI\Control;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationCheckDuplicate extends Control
{


    /**
     * @param ProductParameterGroupTranslationEntity $entity
     * @param ProductParameterGroupTranslationEntity|NULL $duplicateEntity
     * @return ProductParameterGroupTranslationEntity
     * @throws ProductParameterGroupTranslationCheckDuplicateException
     */
    public function check(ProductParameterGroupTranslationEntity $entity, ProductParameterGroupTranslationEntity $duplicateEntity = NULL)
    : ProductParameterGroupTranslationEntity
    {
        if ($duplicateEntity
            && $entity->getLanguageId() == $duplicateEntity->getLanguageId()
            && $entity->getName() == $duplicateEntity->getName()
            && $entity->getId() != $duplicateEntity->getId()
        ) {
            throw new ProductParameterGroupTranslationCheckDuplicateException("Skupina parametrů s názvem '{$entity->getName()}' již existuje.");
        }

        return $entity;
    }
}
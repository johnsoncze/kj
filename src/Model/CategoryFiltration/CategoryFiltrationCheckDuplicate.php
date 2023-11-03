<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationCheckDuplicate extends NObject
{


    /**
     * @param CategoryFiltrationEntity $filtrationEntity
     * @param CategoryFiltrationEntity|NULL $duplicateEntity
     * @return CategoryFiltrationEntity
     * @throws CategoryFiltrationCheckDuplicateException
     */
    public function check(CategoryFiltrationEntity $filtrationEntity,
                          CategoryFiltrationEntity $duplicateEntity = NULL)
    : CategoryFiltrationEntity
    {
        if ($duplicateEntity !== NULL
            && (int)$filtrationEntity->getCategoryId() === (int)$duplicateEntity->getCategoryId()
            && (int)$filtrationEntity->getProductParameterGroupId() === (int)$duplicateEntity->getProductParameterGroupId()
            && (int)$filtrationEntity->getId() !== (int)$duplicateEntity->getId()
        ) {
            throw new CategoryFiltrationCheckDuplicateException(sprintf("Filtrace je již nastavena."));
        }

        return $filtrationEntity;
    }
}
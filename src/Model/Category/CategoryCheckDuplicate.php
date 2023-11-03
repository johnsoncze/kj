<?php

declare(strict_types = 1);

namespace App\Category;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryCheckDuplicate extends NObject
{


    /**
     * @param CategoryEntity $category
     * @param CategoryEntity|NULL $duplicateCategory
     * @return CategoryEntity
     * @throws CategoryCheckDuplicateException
     */
    public function checkName(CategoryEntity $category, CategoryEntity $duplicateCategory = NULL)
    : CategoryEntity
    {
        if ($duplicateCategory !== NULL
            && $category->getName() == $duplicateCategory->getName()
            && $category->getLanguageId() == $duplicateCategory->getLanguageId()
            && $category->getId() != $duplicateCategory->getId()
        ) {
            throw new CategoryCheckDuplicateException(sprintf("Kategorie s názvem '%s' již existuje.",
                $category->getName()));
        }

        return $category;
    }



    /**
     * @param CategoryEntity $category
     * @param CategoryEntity|NULL $duplicateCategory
     * @return CategoryEntity
     * @throws CategoryCheckDuplicateException
     */
    public function checkUrl(CategoryEntity $category, CategoryEntity $duplicateCategory = NULL)
    : CategoryEntity
    {
        if ($duplicateCategory !== NULL
            && $category->getUrl() == $duplicateCategory->getUrl()
            && $category->getLanguageId() == $duplicateCategory->getLanguageId()
            && $category->getId() != $duplicateCategory->getId()
        ) {
            throw new CategoryCheckDuplicateException(sprintf("Kategorie s url '%s' již existuje.",
                $category->getUrl()));
        }

        return $category;
    }
}
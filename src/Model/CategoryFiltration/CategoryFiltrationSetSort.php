<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationSetSort extends NObject
{


    /**
     * @param CategoryFiltrationEntity $categoryFiltrationEntity
     * @param int $sort
     * @return CategoryFiltrationEntity
     */
    public function setSort(CategoryFiltrationEntity $categoryFiltrationEntity, int $sort) : CategoryFiltrationEntity
    {
        $categoryFiltrationEntity->setSort($sort);
        return $categoryFiltrationEntity;
    }
}
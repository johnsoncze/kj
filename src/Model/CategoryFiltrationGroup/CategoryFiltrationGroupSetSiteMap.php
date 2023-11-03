<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupSetSiteMap extends NObject
{


    /**
     * @param CategoryFiltrationGroupEntity $entity
     * @return CategoryFiltrationGroupEntity
     */
    public function set(CategoryFiltrationGroupEntity $entity) : CategoryFiltrationGroupEntity
    {
        $entity->setSiteMap($entity->getIndexSeo() ? TRUE : FALSE);
        return $entity;
    }
}
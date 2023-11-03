<?php

declare(strict_types = 1);

namespace App\CategoryFiltration;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationEntityFactory extends NObject
{


    /**
     * @param int $categoryId
     * @param int $productParameterGroupId
     * @param bool $index
     * @param bool $follow
     * @param bool $siteMap
     * @return CategoryFiltrationEntity
     */
    public function create(int $categoryId, int $productParameterGroupId, bool $index = NULL,
                           bool $follow = NULL, bool $siteMap = NULL) : CategoryFiltrationEntity
    {
        $filtration = new CategoryFiltrationEntity();
        $filtration->setCategoryId($categoryId);
        $filtration->setProductParameterGroupId($productParameterGroupId);
        $filtration->setIndexSeo($index);
        $filtration->setFollowSeo($follow);
        $filtration->setSiteMap($siteMap);

        return $filtration;
    }
}
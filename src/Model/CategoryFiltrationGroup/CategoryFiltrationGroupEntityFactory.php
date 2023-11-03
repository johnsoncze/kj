<?php

declare(strict_types = 1);

namespace App\CategoryFiltrationGroup;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryFiltrationGroupEntityFactory extends NObject
{


    /**
     * @param $categoryId int
     * @param string|NULL $description
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
     * @param bool|NULL $index
     * @param bool|NULL $siteMap
     * @param $follow bool|NULL
     * @param string $status
     * @return CategoryFiltrationGroupEntity
     */
    public function create(int $categoryId,
                           string $description = NULL,
                           string $titleSeo = NULL,
                           string $descriptionSeo = NULL,
                           bool $index = NULL,
                           bool $siteMap = NULL,
                           bool $follow = NULL,
                           string $status) : CategoryFiltrationGroupEntity
    {
        $entity = new CategoryFiltrationGroupEntity();
        $entity->setCategoryId($categoryId);
        $entity->setDescription($description);
        $entity->setTitleSeo($titleSeo);
        $entity->setDescriptionSeo($descriptionSeo);
        $entity->setIndexSeo($index);
        $entity->setFollowSeo($follow);
        $entity->setSiteMap($siteMap);
        $entity->setStatus($status);

        return $entity;
    }
}
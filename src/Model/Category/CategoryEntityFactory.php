<?php

declare(strict_types = 1);

namespace App\Category;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryEntityFactory extends NObject
{


    /**
     * @param int $languageId
     * @param $parentId int|null
     * @param string $name
     * @param $content string|null
     * @param string|NULL $url
     * @param string|NULL $titleSeo
     * @param string|NULL $descriptionSeo
     * @param int|NULL $sort
     * @param string $status
     * @return CategoryEntity
     */
    public function create(int $languageId, int $parentId = NULL, string $name, string $content = NULL, string $url = NULL,
                           string $titleSeo = NULL, string $descriptionSeo = NULL, int $sort = NULL, string $status) : CategoryEntity
    {
        $entity = new CategoryEntity();
        $entity->setLanguageId($languageId);
        $entity->setParentCategoryId($parentId);
        $entity->setName($name);
        $entity->setContent($content);
        $entity->setUrl($url);
        $entity->setTitleSeo($titleSeo);
        $entity->setDescriptionSeo($descriptionSeo);
        $entity->setCategorySliderSort("127");
        $entity->setHomepageSort($sort);
        $entity->setStatus($status);

        return $entity;
    }
}

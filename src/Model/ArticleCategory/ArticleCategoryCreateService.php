<?php

namespace App\ArticleCategory;

use App\NObject;


class ArticleCategoryCreateService extends NObject
{


    /**
     * @param $langId int
     * @param $moduleId int
     * @param $name string
     * @param $url string|null
     * @param $titleSeo string|null
     * @param $descriptionSeo string|null
     * @return ArticleCategoryEntity
     */
    public function createEntity($langId, int $moduleId, $name, $url = null, $titleSeo = null, $descriptionSeo = null)
    {
        $entity = new ArticleCategoryEntity();
        $entity->setLanguageId($langId);
        $entity->setModuleId($moduleId);
        $entity->setUrl($url);
        $entity->setName($name);
        $entity->setUrl($url);
        $entity->setTitleSeo($titleSeo);
        $entity->setDescriptionSeo($descriptionSeo);
        return $entity;
    }
}
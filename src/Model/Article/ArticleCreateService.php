<?php

namespace App\Article;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCreateService extends NObject
{


    /**
     * @param $langId int
     * @param $name string
     * @param $url string|null
     * @param $titleSeo string|null
     * @param $descriptionSeo string|null
     * @param $coverPhoto null|string
     * @param $introduction string
     * @param $content string
     * @param $status string
     * @return ArticleEntity
     */
    public function createEntity($langId,
                                 $name,
                                 $url = null,
                                 $titleSeo = null,
                                 $descriptionSeo = null,
                                 $coverPhoto = null,
                                 $introduction,
                                 $content,
                                 $status)
    {
        $entity = new ArticleEntity();
        $entity->setLanguageId($langId);
        $entity->setUrl($url);
        $entity->setName($name);
        $entity->setUrl($url);
        $entity->setTitleSeo($titleSeo);
        $entity->setDescriptionSeo($descriptionSeo);
        $entity->setCoverPhoto($coverPhoto);
        $entity->setIntroduction($introduction);
        $entity->setContent($content);
        $entity->setStatus($status);
        return $entity;
    }
}
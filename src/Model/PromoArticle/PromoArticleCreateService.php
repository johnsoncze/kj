<?php

namespace App\PromoArticle;

use App\NObject;



class PromoArticleCreateService extends NObject
{


    /**
     * @param $title string
     * @param $photo null|string
     * @param $text string
     * @param $url string
     * @param $urlText string
     * @param $sequence int
     * @param $isDefault int
     * @return PromoArticleEntity
     */
    public function createEntity($title,
                                 $photo = null,
                                 $text,
                                 $url,
                                 $urlText,
                                 $sequence,
																$isDefault)
    {
        $entity = new PromoArticleEntity();
        $entity->setTitle($title);
        $entity->setPhoto($photo);
        $entity->setText($text);
        $entity->setUrl($url);
        $entity->setUrlText($urlText);
        $entity->setSequence($sequence);
        $entity->setIsDefault($isDefault);
        return $entity;
    }
}
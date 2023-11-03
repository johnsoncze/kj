<?php

namespace App\Article;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleDuplicateService extends NObject
{


    /**
     * @param $entity ArticleEntity
     * @param $entity2 null|ArticleEntity
     * @return ArticleEntity
     * @throws ArticleDuplicateServiceException
     */
    public function checkName(ArticleEntity $entity, $entity2 = null)
    {
        if ($entity2 instanceof ArticleEntity && $entity->getId() != $entity2->getId() && $entity->getName() == $entity2->getName()) {
            throw new ArticleDuplicateServiceException("Článek s názvem '{$entity->getName()}' již existuje.");
        }
        return $entity;
    }



    /**
     * @param $entity ArticleEntity
     * @param $entity2 null|ArticleEntity
     * @return ArticleEntity
     * @throws ArticleDuplicateServiceException
     */
    public function checkUrl(ArticleEntity $entity, ArticleEntity $entity2 = null)
    {
        if ($entity2 instanceof ArticleEntity && $entity->getId() != $entity2->getId() && $entity->getUrl() == $entity2->getUrl()) {
            throw new ArticleDuplicateServiceException("Článek s URL adresou '{$entity->getUrl()}' již existuje.");
        }
        return $entity;
    }
}

class ArticleDuplicateServiceException extends \Exception
{


}
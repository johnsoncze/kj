<?php

namespace App\ArticleCategory;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryDuplicateService extends NObject
{


    /**
     * @param $entity ArticleCategoryEntity
     * @param $entity2 null|ArticleCategoryEntity
     * @return ArticleCategoryEntity
     * @throws ArticleCategoryDuplicateServiceException
     */
    public function checkName(ArticleCategoryEntity $entity, $entity2 = null)
    {
        if ($entity2 instanceof ArticleCategoryEntity && $entity->getId() != $entity2->getId() && $entity->getName() == $entity2->getName()) {
            throw new ArticleCategoryDuplicateServiceException("Rubrika s názvem '{$entity->getName()}' již existuje.");
        }
        return $entity;
    }



    /**
     * @param $entity ArticleCategoryEntity
     * @param $entity2 null|ArticleCategoryEntity
     * @return ArticleCategoryEntity
     * @throws ArticleCategoryDuplicateServiceException
     */
    public function checkUrl(ArticleCategoryEntity $entity, $entity2 = null)
    {
        if ($entity2 instanceof ArticleCategoryEntity && $entity->getId() != $entity2->getId() && $entity->getUrl() == $entity2->getUrl()) {
            throw new ArticleCategoryDuplicateServiceException("Rubrika s URL adresou '{$entity->getUrl()}' již existuje.");
        }
        return $entity;
    }
}

class ArticleCategoryDuplicateServiceException extends \Exception
{


}
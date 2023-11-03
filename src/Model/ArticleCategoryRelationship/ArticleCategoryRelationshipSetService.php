<?php

namespace App\ArticleCategory;

use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRelationshipSetService
{


    /**
     * @param $entity ArticleCategoryRelationshipEntity
     * @param $id int
     * @return ArticleCategoryRelationshipEntity
     */
    public function setArticleCategoryId(ArticleCategoryRelationshipEntity $entity, $id)
    {
        $entity->setArticleCategoryId($id);
        return $entity;
    }
}
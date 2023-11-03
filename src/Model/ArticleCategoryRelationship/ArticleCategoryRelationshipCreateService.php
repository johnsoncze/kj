<?php
/**
 * Created by PhpStorm.
 * User: dusanmlynarcik
 * Date: 02.01.17
 * Time: 22:04
 */
declare(strict_types = 1);


namespace App\ArticleCategoryRelationship;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ArticleCategoryRelationshipCreateService
{


    /**
     * @param int $articleId
     * @param int $categoryId
     * @return ArticleCategoryRelationshipEntity
     */
    public function create(int $articleId, int $categoryId) : ArticleCategoryRelationshipEntity
    {
        $entity = new ArticleCategoryRelationshipEntity();
        $entity->setArticleId($articleId);
        $entity->setArticleCategoryId($categoryId);
        return $entity;
    }
}
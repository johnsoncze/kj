<?php

namespace App\ArticleCategoryRelationship;

use App\BaseEntity;
use Ricaefeliz\Mappero\Entities\IEntity;


/**
 * @Table(name="article_category_relationship")
 *
 * @method setId($id)
 * @method getId()
 * @method setArticleCategoryId($id)
 * @method getArticleCategoryId()
 * @method setArticleId($id)
 * @method getArticleId()
 * @method setCategory($category)
 * @method getCategory()
 */
class ArticleCategoryRelationshipEntity extends BaseEntity implements IEntity
{


    /**
     * @Column(name="acr_id", key="Primary")
     * @var int
     */
    protected $id;

    /**
     * @Column(name="acr_article_category_id")
     * @var int
     */
    protected $articleCategoryId;

    /**
     * @Column(name="acr_article_id")
     * @var int
     */
    protected $articleId;

    /**
     * @OneToOne(entity="App\ArticleCategory\ArticleCategoryEntity")
     */
    protected $category;
}
<?php

namespace App\ArticleCategoryRelationship;


use App\NObject;


/**
 * @method getArticleCategoryId()
 * @method getArticlesCount()
 */
class CategoryArticlesCount extends NObject
{


    /** @var int */
    protected $articleCategoryId;

    /** @var int */
    protected $articlesCount;



    public function __construct($articleCategoryId, $articlesCount)
    {
        $this->articleCategoryId = $articleCategoryId;
        $this->articlesCount = $articlesCount;
    }



    /**
     * @param $params \ArrayAccess
     * @return self
     */
    public static function create(\ArrayAccess $params)
    {
        return new static($params["articleCategoryId"], $params["articlesCount"]);
    }
}
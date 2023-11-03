<?php

declare(strict_types = 1);

namespace App\Tests\ArticleCategory;

use App\ArticleCategory\ArticleCategoryEntity;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ArticleCategoryTestTrait
{


    /**
     * @return ArticleCategoryEntity
     */
    private function createTestArticleCategory() : ArticleCategoryEntity
    {
        $category = new ArticleCategoryEntity();
        $category->setLanguageId(1);
        $category->setModuleId(1);
        $category->setName('Category');
        $category->setUrl(Strings::webalize($category->getName()));

        return $category;
    }
}
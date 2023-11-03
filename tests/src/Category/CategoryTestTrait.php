<?php

declare(strict_types = 1);

namespace App\Tests\Category;

use App\Category\CategoryEntity;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait CategoryTestTrait
{


    /**
     * @return CategoryEntity
     */
    private function createTestCategory() : CategoryEntity
    {
        $category = new CategoryEntity();
        $category->setLanguageId(1);
        $category->setName('Category');
        $category->setUrl(Strings::webalize($category->getName()));
        $category->setContent('Description of category');
        $category->setShowOnHomepage(FALSE);
        $category->setCategorySlider(FALSE);
        $category->setSort(CategoryEntity::PUBLISH);

        return $category;
    }
}
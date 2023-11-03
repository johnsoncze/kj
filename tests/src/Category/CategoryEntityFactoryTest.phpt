<?php

declare(strict_types = 1);

namespace App\Tests\Category;

use App\Category\CategoryEntity;
use App\Category\CategoryEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryEntityFactoryTest extends BaseTestCase
{


    public function testCreateSuccess()
    {
        $languageId = 5;
        $parentId = 70;
        $name = 'Kategory pro produkty';
        $content = 'Toto je obsah kategorie';
        $url = 'url kategorie';
        $titleSeo = 'Titulek pro SEO';
        $descriptionSeo = 'Popis pro SEO';
        $sort = 20;
        $status = CategoryEntity::PUBLISH;

        $factory = new CategoryEntityFactory();
        $category = $factory->create($languageId, $parentId, $name, $content,
            $url, $titleSeo, $descriptionSeo, $sort, $status);

        Assert::same($languageId, (int)$category->getLanguageId());
        Assert::same($parentId, (int)$category->getParentCategoryId());
        Assert::same($name, $category->getName());
        Assert::same($content, $category->getContent());
        Assert::same($url, $category->getUrl());
        Assert::same($titleSeo, $category->getTitleSeo());
        Assert::same($descriptionSeo, $category->getDescriptionSeo());
        Assert::same((int)($languageId . $sort), (int)$category->getSort());
        Assert::same($status, $category->getStatus());
    }



    public function testCreateWithoutNullableParameters()
    {
        $languageId = 5;
        $name = 'Kategory pro produkty';
        $status = CategoryEntity::PUBLISH;

        $factory = new CategoryEntityFactory();
        $category = $factory->create($languageId, NULL, $name, NULL, NULL,
            NULL, NULL, NULL, $status);

        Assert::same($languageId, (int)$category->getLanguageId());
        Assert::same($name, $category->getName());
        Assert::same($status, $category->getStatus());
    }
}

(new CategoryEntityFactoryTest())->run();
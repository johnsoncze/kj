<?php

declare(strict_types = 1);

namespace App\Tests\Category;

use App\Category\CategoryCheckDuplicate;
use App\Category\CategoryCheckDuplicateException;
use App\Category\CategoryEntity;
use App\Category\CategoryEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryCheckDuplicateTest extends BaseTestCase
{


    public function testCheckNameWithNoDuplicateCategory()
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

        $checker = new CategoryCheckDuplicate();

        Assert::type(CategoryEntity::class, $checker->checkName($category));
    }



    public function testCheckNameWithSecondNoDuplicateCategory()
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

        $category2 = clone $category;
        $category2->setLanguageId(3);

        $category3 = clone $category;
        $category3->setName("Jiný název");

        $checker = new CategoryCheckDuplicate();

        Assert::type(CategoryEntity::class, $checker->checkName($category, $category2));
        Assert::type(CategoryEntity::class, $checker->checkName($category, $category3));
    }



    public function testCheckNameWithDuplicateCategory()
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

        Assert::exception(function () use ($category) {
            $category2 = clone $category;
            $category2->setId(55);

            $checker = new CategoryCheckDuplicate();
            $checker->checkName($category, $category2);
        }, CategoryCheckDuplicateException::class, sprintf("Kategorie s názvem '%s' již existuje.",
            $category->getName()));
    }



    public function testCheckUrlWithNoDuplicateCategory()
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

        $checker = new CategoryCheckDuplicate();

        Assert::type(CategoryEntity::class, $checker->checkUrl($category));
    }



    public function testCheckUrlWithSecondNoDuplicateCategory()
    {
        $languageId = 5;
        $parentId = 70;
        $name = 'Kategory pro produkty';
        $content = 'Toto je obsah kategorie';
        $url = 'url-kategorie';
        $titleSeo = 'Titulek pro SEO';
        $descriptionSeo = 'Popis pro SEO';
        $sort = 20;
        $status = CategoryEntity::PUBLISH;

        $factory = new CategoryEntityFactory();
        $category = $factory->create($languageId, $parentId, $name, $content,
            $url, $titleSeo, $descriptionSeo, $sort, $status);

        $category2 = clone $category;
        $category2->setLanguageId(3);

        $category3 = clone $category;
        $category3->setUrl('jina-url');

        $checker = new CategoryCheckDuplicate();

        Assert::type(CategoryEntity::class, $checker->checkName($category, $category2));
        Assert::type(CategoryEntity::class, $checker->checkName($category, $category3));
    }



    public function testCheckUrlWithDuplicateCategory()
    {
        $languageId = 5;
        $parentId = 70;
        $name = 'Kategory pro produkty';
        $content = 'Toto je obsah kategorie';
        $url = 'url-kategorie';
        $titleSeo = 'Titulek pro SEO';
        $descriptionSeo = 'Popis pro SEO';
        $sort = 20;
        $status = CategoryEntity::PUBLISH;

        $factory = new CategoryEntityFactory();
        $category = $factory->create($languageId, $parentId, $name, $content,
            $url, $titleSeo, $descriptionSeo, $sort, $status);

        Assert::exception(function () use ($category) {
            $category2 = clone $category;
            $category2->setId(55);

            $checker = new CategoryCheckDuplicate();
            $checker->checkUrl($category, $category2);
        }, CategoryCheckDuplicateException::class, sprintf("Kategorie s url '%s' již existuje.",
            $category->getUrl()));
    }
}

(new CategoryCheckDuplicateTest())->run();
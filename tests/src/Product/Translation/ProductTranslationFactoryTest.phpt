<?php

declare(strict_types = 1);

namespace App\Tests\Product\Translation;

use App\Product\Translation\ProductTranslationFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationFactoryTest extends BaseTestCase
{


    public function testCreate()
    {
        $productId = 5;
        $languageId = 10;
        $name = 'Name of product';
        $description = 'This is a test description';
        $url = 'url-address';
        $titleSeo = 'Title of seo';
        $descriptionSeo = 'Description od seo';

        $translationFactory = new ProductTranslationFactory();
        $translation = $translationFactory->create($productId, $languageId, $name, $description, $url, $titleSeo, $descriptionSeo);

        Assert::same($productId, $translation->getProductId());
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($name, $translation->getName());
        Assert::same($description, $translation->getDescription());
        Assert::same($url, $translation->getUrl());
        Assert::same($titleSeo, $translation->getTitleSeo());
        Assert::same($descriptionSeo, $translation->getDescriptionSeo());
    }
}

(new ProductTranslationFactoryTest())->run();
<?php

declare(strict_types = 1);

namespace App\Tests\Product\Translation;


use App\Product\ProductExistsAlreadyException;
use App\Product\Translation\ProductTranslationDuplicate;
use App\Product\Translation\ProductTranslationFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductTranslationDuplicateTest extends BaseTestCase
{


    public function testCheckDuplicateWithoutSecondTranslation()
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

        $checker = new ProductTranslationDuplicate();

        Assert::same($translation, $checker->checkUrl($translation));
    }



    public function testCheckDuplicateWithSecondTranslationWithSameUrl()
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
        $cloneTranslation = $translationFactory->create($productId, $languageId + 1, $name, $description, $url, $titleSeo, $descriptionSeo);
        $cloneTranslation->setId(1);

        $checker = new ProductTranslationDuplicate();

        Assert::same($translation, $checker->checkUrl($translation, $cloneTranslation));
    }



    public function testCheckDuplicateWithSecondTranslationWithSameLanguageId()
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
        $cloneTranslation = clone $translation;
        $cloneTranslation->setId(1);
        $cloneTranslation->setUrl('another-url');

        $checker = new ProductTranslationDuplicate();

        Assert::same($translation, $checker->checkUrl($translation, $cloneTranslation));
    }



    public function testCheckDuplicateWithSameTranslation()
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

        $checker = new ProductTranslationDuplicate();

        Assert::same($translation, $checker->checkUrl($translation, $translation));
    }



    public function testCheckDuplicateWithDuplicateTranslation()
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
        $cloneTranslation = clone $translation;
        $cloneTranslation->setId(5);

        $checker = new ProductTranslationDuplicate();

        Assert::exception(function () use ($translation, $cloneTranslation, $checker) {
            $checker->checkUrl($translation, $cloneTranslation);
        }, ProductExistsAlreadyException::class, sprintf('Produkt s url "%s" pro jazyk s id "%d" již existuje.', $url, $languageId));
    }
}

(new ProductTranslationDuplicateTest())->run();
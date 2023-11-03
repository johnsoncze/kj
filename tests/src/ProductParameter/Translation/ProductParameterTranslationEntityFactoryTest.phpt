<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterTranslationEntity;
use App\ProductParameter\ProductParameterTranslationEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationEntityFactoryTest extends BaseTestCase
{


    public function testCreateEntitySuccessWithoutUrl()
    {
        $productParameterId = 44;
        $languageId = 4;
        $value = "Dřevo";

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create($productParameterId, $languageId, $value);

        Assert::type(ProductParameterTranslationEntity::class, $translation);
        Assert::same($productParameterId, $translation->getProductParameterId());
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($value, $translation->getValue());
        Assert::null($translation->getUrl());
    }



    public function testCreateEntitySuccessWithUrl()
    {
        $productParameterId = 44;
        $languageId = 4;
        $value = "Dřevo";
        $url = "drevo";

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create($productParameterId, $languageId, $value, $url);

        Assert::type(ProductParameterTranslationEntity::class, $translation);
        Assert::same($productParameterId, $translation->getProductParameterId());
        Assert::same($languageId, $translation->getLanguageId());
        Assert::same($value, $translation->getValue());
        Assert::same($url, $translation->getUrl());
    }
}

(new ProductParameterTranslationEntityFactoryTest())->run();
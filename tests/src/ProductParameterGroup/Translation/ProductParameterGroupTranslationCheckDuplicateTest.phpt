<?php

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupTranslationCheckDuplicate;
use App\ProductParameterGroup\ProductParameterGroupTranslationCheckDuplicateException;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationCheckDuplicateTest extends BaseTestCase
{


    public function testWithoutSecondEntity()
    {
        $translation = new ProductParameterGroupTranslationEntity();
        $translation->setId(12);
        $translation->setLanguageId(43);
        $translation->setName("Materials");

        $checker = new ProductParameterGroupTranslationCheckDuplicate();
        $result = $checker->check($translation);

        Assert::same($translation, $result);
    }



    public function testWithSecondNotDuplicateEntity()
    {
        $languageId = 43;

        $translation = new ProductParameterGroupTranslationEntity();
        $translation->setLanguageId($languageId);
        $translation->setName("Materials");

        $translation2 = new ProductParameterGroupTranslationEntity();
        $translation2->setId(77);
        $translation2->setLanguageId($languageId);
        $translation2->setName("Another name");

        $checker = new ProductParameterGroupTranslationCheckDuplicate();
        $result = $checker->check($translation, $translation2);

        Assert::same($translation, $result);
    }



    public function testWithDuplicateEntity()
    {
        $languageId = 43;
        $name = "Materials";

        $translation = new ProductParameterGroupTranslationEntity();
        $translation->setLanguageId($languageId);
        $translation->setName($name);

        $translation2 = new ProductParameterGroupTranslationEntity();
        $translation2->setId(12);
        $translation2->setLanguageId($languageId);
        $translation2->setName($name);

        Assert::exception(function () use ($translation, $translation2) {
            $checker = new ProductParameterGroupTranslationCheckDuplicate();
            $checker->check($translation, $translation2);
        }, ProductParameterGroupTranslationCheckDuplicateException::class);
    }
}

(new ProductParameterGroupTranslationCheckDuplicateTest())->run();
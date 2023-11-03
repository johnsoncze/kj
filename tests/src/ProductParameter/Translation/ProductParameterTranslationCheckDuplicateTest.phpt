<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterTranslationCheckDuplicate;
use App\ProductParameter\ProductParameterTranslationCheckDuplicateException;
use App\ProductParameter\ProductParameterTranslationEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterTranslationCheckDuplicateTest extends BaseTestCase
{


    public function testCheckWithoutDuplicate()
    {
        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(55);

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create((int)$parameter->getId(), 4, "Dřevo");

        $duplicateChecker = new ProductParameterTranslationCheckDuplicate();
        $result = $duplicateChecker->check($parameter, $translation);

        Assert::same($translation, $result);
    }



    public function testCheckWithSecondNotDuplicateTranslation()
    {
        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(55);
        $parameter2 = $parameterFactory->create(55);

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create((int)$parameter->getId(), 4, "Dřevo");
        $translation2 = $translationFactory->create((int)$parameter2->getId(), 4, "Plech");
        $translation2->setId(79);

        $duplicateChecker = new ProductParameterTranslationCheckDuplicate();
        $result = $duplicateChecker->check($parameter, $translation, $parameter2, $translation2);

        Assert::same($translation, $result);
    }



    public function testCheckWithDuplicateTranslation()
    {
        $value = "Dřevo";

        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(55);
        $parameter2 = $parameterFactory->create(55);

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create((int)$parameter->getId(), 4, $value);
        $translation2 = $translationFactory->create((int)$parameter2->getId(), 4, $value);
        $translation2->setId(79);

        Assert::exception(function () use ($parameter, $translation, $parameter2, $translation2) {
            $duplicateChecker = new ProductParameterTranslationCheckDuplicate();
            $duplicateChecker->check($parameter, $translation, $parameter2, $translation2);
        }, ProductParameterTranslationCheckDuplicateException::class);
    }



    public function testCheckDuplicateWithOtherLanguage()
    {
        $value = "Dřevo";

        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(55);
        $parameter2 = $parameterFactory->create(55);

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create((int)$parameter->getId(), 4, $value);
        $translation2 = $translationFactory->create((int)$parameter2->getId(), 3, $value);
        $translation2->setId(79);

        $duplicateChecker = new ProductParameterTranslationCheckDuplicate();
        $result = $duplicateChecker->check($parameter, $translation, $parameter2, $translation2);

        Assert::same($translation, $result);
    }



    public function testCheckDuplicateWithAnotherParameter()
    {
        $value = "Dřevo";

        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(55);
        $parameter2 = $parameterFactory->create(66);

        $translationFactory = new ProductParameterTranslationEntityFactory();
        $translation = $translationFactory->create((int)$parameter->getId(), 4, $value);
        $translation2 = $translationFactory->create((int)$parameter2->getId(), 4, $value);
        $translation2->setId(79);

        $duplicateChecker = new ProductParameterTranslationCheckDuplicate();
        $result = $duplicateChecker->check($parameter, $translation, $parameter2, $translation2);

        Assert::same($translation, $result);
    }
}

(new ProductParameterTranslationCheckDuplicateTest())->run();
<?php

namespace App\Tests\Mappero\Translation;

use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Exceptions\TranslationMissingException;
use Ricaefeliz\Mappero\Mapping\Translation\TestEntity;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;
use Ricaefeliz\Mappero\Translation\TranslationConfig;
use Tester\Assert;


require_once __DIR__ . "/../../../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationEntityTest extends BaseTestCase
{


    public function testTranslation()
    {
        $config = new TranslationConfig(["cs" => 44, "hu" => 55], "hu");
        LocalizationResolver::setConfig($config);

        $translation = new TestTranslationEntity();
        $translation->setLanguageId(55);

        $translatable = new TestEntity();
        $translatable->addTranslation($translation);

        $translationFromEntity = $translatable->getTranslation();

        Assert::type(TestTranslationEntity::class, $translationFromEntity);
        Assert::same($translationFromEntity->getLanguageId(), $translation->getLanguageId());
        Assert::exception(function () use ($translatable) {
            $translatable->getTranslation("halala");
        }, TranslationMissingException::class);
    }
}

(new TranslationEntityTest())->run();
<?php

namespace App\Tests\Mappero\Translation;

require_once __DIR__ . "/../../bootstrap.php";

use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Translation\Localization;
use Ricaefeliz\Mappero\Translation\LocalizationResolver;
use Ricaefeliz\Mappero\Translation\TranslationConfig;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationResolverTest extends BaseTestCase
{


    public function testGet()
    {
        $whiteList = [
            "cs" => 45,
            "en" => 55,
            "hu" => 78
        ];
        $config = new TranslationConfig($whiteList, "en");
        LocalizationResolver::setConfig($config);

        $resolver = new LocalizationResolver();
        $actual = $resolver->getActual();
        $csLocation = $resolver->getByPrefix("cs");

        Assert::type(Localization::class, $actual);
        Assert::same(55, $actual->getId());
        Assert::same("en", $actual->getPrefix());

        Assert::same(78, $resolver->getId("hu"));

        Assert::type(Localization::class, $csLocation);
        Assert::same($csLocation->getId(), 45);
        Assert::same($csLocation->getPrefix(), "cs");
    }

}

(new TranslationResolverTest())->run();
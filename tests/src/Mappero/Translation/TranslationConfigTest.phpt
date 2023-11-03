<?php

namespace App\Tests\Mappero\Translation;

require_once __DIR__ . "/../../bootstrap.php";

use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Translation\TranslationConfig;
use Ricaefeliz\Mappero\Translation\TranslationConfigException;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class TranslationConfigTest extends BaseTestCase
{


    public function tesCreateTranslationConfigFail()
    {
        $whiteList = ["cs" => 55, "en" => 78, "hu" => 77];

        Assert::exception(function () use ($whiteList) {
            $config = new TranslationConfig($whiteList, "mu");
        }, TranslationConfigException::class);
    }



    public function testGet()
    {
        $whiteList = ["cs" => 55, "en" => 78, "hu" => 77];
        $config = new TranslationConfig($whiteList, "cs");

        Assert::same($config->getActual(), "cs");
        Assert::same($config->getDefault(), "cs");
        Assert::same($config->getWhiteList(), $whiteList);
    }
}

(new TranslationConfigTest())->run();
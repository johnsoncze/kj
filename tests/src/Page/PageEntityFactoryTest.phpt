<?php

namespace App\Tests\Page;

require_once __DIR__ . "/../bootstrap.php";

use App\Page\PageEntity;
use App\Page\PageEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageEntityFactoryTest extends BaseTestCase
{
    public function testCreate()
    {
        $langId = 7;
        $parentPageId = 9;
        $type = PageEntity::ARTICLES_TYPE;
        $name = "Název stránky";
        $content = "Obsah stránky!..";
        $url = "url-stranky";
        $titleSeo = "Titulek pro seo";
        $descriptionSeo = "Popis pro seo";
        $setting = ["ahoj" => TRUE, "klíč" => "hodnota", "klíč2" => 23];
        $status = PageEntity::DRAFT;

        $factory = new PageEntityFactory();
        $entity = $factory->create($langId, $parentPageId, $type, $name,
            $content, $url, $titleSeo, $descriptionSeo, $setting, $status);

        Assert::same($langId, $entity->getLanguageId());
        Assert::same($parentPageId, $entity->getParentPageId());
        Assert::same($type, $entity->getType());
        Assert::same($name, $entity->getName());
        Assert::same($content, $entity->getContent());
        Assert::same($url, $entity->getUrl());
        Assert::same($titleSeo, $entity->getTitleSeo());
        Assert::same($descriptionSeo, $entity->getDescriptionSeo());
        Assert::same($setting, $entity->getSetting(TRUE));
        Assert::same($status, $entity->getStatus());
    }
}

(new PageEntityFactoryTest())->run();
<?php

namespace App\Tests\Page;

require_once __DIR__ . "/../bootstrap.php";

use App\Page\PageEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageEntityTest extends BaseTestCase
{


    public function testCreate()
    {
        $id = 1;
        $languageId = 5;
        $parentPageId = 7;
        $type = \App\Page\PageEntity::TEXT_TYPE;
        $name = "Stránka název";
        $content = "Obsah stránky.";
        $url = "url-stranka";
        $titleSeo = "Titulek pro seo";
        $descriptionSeo = "Popis pro seo";
        $status = \App\Page\PageEntity::PUBLISH;
        $setting = ["pocet_clanku" => 3, "dalsi_nastaveni" => TRUE, "dalsi_nas" => "hodnota"];

        $page = new \App\Page\PageEntity();
        $page->setId($id);
        $page->setLanguageId($languageId);
        $page->setParentPageId($parentPageId);
        $page->setType($type);
        $page->setName($name);
        $page->setContent($content);
        $page->setUrl($url);
        $page->setTitleSeo($titleSeo);
        $page->setDescriptionSeo($descriptionSeo);
        $page->setStatus($status);
        $page->setSetting($setting);

        Assert::same($id, $page->getId());
        Assert::same($languageId, $page->getLanguageId());
        Assert::same($parentPageId, $page->getParentPageId());
        Assert::same($type, $page->getType());
        Assert::same($name, $page->getName());
        Assert::same($content, $page->getContent());
        Assert::same($url, $page->getUrl());
        Assert::same($titleSeo, $page->getTitleSeo());
        Assert::same($descriptionSeo, $page->getDescriptionSeo());
        Assert::same($status, $page->getStatus());
        Assert::same($setting, $page->getSetting(TRUE));

        Assert::same([PageEntity::TEXT_TYPE, PageEntity::ARTICLES_TYPE], array_keys(PageEntity::getTypes()));
    }
}

(new PageEntityTest())->run();
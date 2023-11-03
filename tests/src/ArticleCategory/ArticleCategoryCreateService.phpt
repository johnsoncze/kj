<?php

namespace App\Tests\ArticleCategory;

use App\ArticleCategory\ArticleCategoryCreateServiceFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";


class ArticleCategoryCreateService extends BaseTestCase
{


    public function testCreateSuccess()
    {
        $service = $this->container->getByType(ArticleCategoryCreateServiceFactory::class)
            ->create();

        $langId = 457;
        $moduleId = 123;
        $name = "Zahraniční rubrika 1 2 3 česko";
        $titleSeo = "Tituleček pro SEO";
        $descriptionSeo = "Popísek pro SEO.";

        $entity = $service->createEntity($langId, $moduleId, $name, $name, $titleSeo, $descriptionSeo);

        Assert::equal($langId, $entity->getLanguageId());
        Assert::equal($moduleId, $entity->getModuleId());
        Assert::equal($name, $entity->getName());
        Assert::equal($titleSeo, $entity->getTitleSeo());
        Assert::equal($descriptionSeo, $entity->getDescriptionSeo());
    }



    public function testCreateFail()
    {
        $service = $this->container->getByType(ArticleCategoryCreateServiceFactory::class)
            ->create();

        $langId = 457;
        $moduleId = 123;
        $name = "Zahraniční rubrika";
        $titleSeo = "Tituleček pro SEO";
        $descriptionSeo = "Popísek pro SEO.";

        $entity = $service->createEntity($langId, $moduleId, $name, $name, $titleSeo, $descriptionSeo);

        Assert::notEqual(777, $entity->getLanguageId());
        Assert::notEqual("něco", $entity->getName());
        Assert::notEqual("titulek", $entity->getTitleSeo());
        Assert::notEqual("popis", $entity->getDescriptionSeo());
    }
}

(new ArticleCategoryCreateService())->run();
<?php

namespace App\Tests\ArticleCategory;

use App\ArticleCategory\ArticleCategoryDuplicateServiceException;
use App\ArticleCategory\ArticleCategoryDuplicateServiceFactory;
use App\ArticleCategory\ArticleCategoryEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

class ArticleCategoryDuplicateService extends BaseTestCase
{


    public function testCheckNameSuccess()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setName("Zahraničí");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setName("Domácí");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkName($entity1, $entity2));
    }



    public function testCheckNameSuccess2()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(2);
        $entity1->setName("Domácí");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity2->setName("Domácí");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkName($entity1, $entity2));
    }



    public function testCheckNameSuccess3()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(2);
        $entity1->setName("Domácí");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkName($entity1, null));
    }



    public function testCheckNameFail()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setName("Domácí");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity2->setName("Domácí");

        Assert::exception(function () use ($duplicateService, $entity1, $entity2) {
            $duplicateService->checkName($entity1, $entity2);
        }, ArticleCategoryDuplicateServiceException::class);
    }

    /************ Check url ************/

    public function testCheckUrlSuccess()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setUrl("domaci");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkUrl($entity1, $entity2));
    }



    public function testCheckUrlSuccess2()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(2);
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity1->setUrl("zahranici");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkUrl($entity1, $entity2));
    }



    public function testCheckUrlSuccess3()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(2);
        $entity1->setUrl("zahranici");

        Assert::type(ArticleCategoryEntity::class, $duplicateService->checkUrl($entity1, null));
    }



    public function testCheckUrlFail()
    {
        $duplicateService = $this->container->getByType(ArticleCategoryDuplicateServiceFactory::class)->create();

        $entity1 = new ArticleCategoryEntity();
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity2->setUrl("zahranici");

        Assert::exception(function () use ($duplicateService, $entity1, $entity2) {
            $duplicateService->checkUrl($entity1, $entity2);
        }, ArticleCategoryDuplicateServiceException::class);
    }
}

(new ArticleCategoryDuplicateService())->run();
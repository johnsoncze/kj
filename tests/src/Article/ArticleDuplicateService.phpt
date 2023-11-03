<?php

namespace App\Tests\Article;

use App\Article\ArticleDuplicateServiceException;
use App\Article\ArticleEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__."/../bootstrap.php";

class ArticleDuplicateService extends BaseTestCase
{
    public function testCheckNameSuccess()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setName("Zahraničí");

        $entity2 = new ArticleEntity();
        $entity2->setName("Domácí");

        Assert::type(ArticleEntity::class, $duplicateService->checkName($entity1, $entity2));
    }



    public function testCheckNameSuccess2()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setId(2);
        $entity1->setName("Domácí");

        $entity2 = new ArticleEntity();
        $entity2->setId(2);
        $entity2->setName("Domácí");

        Assert::type(ArticleEntity::class, $duplicateService->checkName($entity1, $entity2));
    }



    public function testCheckNameSuccess3()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setId(2);
        $entity1->setName("Domácí");

        Assert::type(ArticleEntity::class, $duplicateService->checkName($entity1, null));
    }



    public function testCheckNameFail()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setName("Domácí");

        $entity2 = new ArticleEntity();
        $entity2->setId(2);
        $entity2->setName("Domácí");

        Assert::exception(function () use ($duplicateService, $entity1, $entity2) {
            $duplicateService->checkName($entity1, $entity2);
        }, ArticleDuplicateServiceException::class);
    }

    /************ Check url ************/

    public function testCheckUrlSuccess()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleEntity();
        $entity2->setUrl("domaci");

        Assert::type(ArticleEntity::class, $duplicateService->checkUrl($entity1, $entity2));
    }



    public function testCheckUrlSuccess2()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setId(2);
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleEntity();
        $entity2->setId(2);
        $entity1->setUrl("zahranici");

        Assert::type(ArticleEntity::class, $duplicateService->checkUrl($entity1, $entity2));
    }



    public function testCheckUrlSuccess3()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setId(2);
        $entity1->setUrl("zahranici");

        Assert::type(ArticleEntity::class, $duplicateService->checkUrl($entity1, null));
    }



    public function testCheckUrlFail()
    {
        $duplicateService = new \App\Article\ArticleDuplicateService();

        $entity1 = new ArticleEntity();
        $entity1->setUrl("zahranici");

        $entity2 = new ArticleEntity();
        $entity2->setId(2);
        $entity2->setUrl("zahranici");

        Assert::exception(function () use ($duplicateService, $entity1, $entity2) {
            $duplicateService->checkUrl($entity1, $entity2);
        }, ArticleDuplicateServiceException::class);
    }
}

(new ArticleDuplicateService())->run();
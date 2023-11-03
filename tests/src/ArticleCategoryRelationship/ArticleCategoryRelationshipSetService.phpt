<?php

namespace App\Tests\ArticleCategoryRelationship;

use App\ArticleCategoryRelationship\ArticleCategoryRelationshipEntity;
use App\ArticleCategoryRelationship\ArticleCategoryRelationshipSetServiceFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

class ArticleCategoryRelationshipSetService extends BaseTestCase
{


    public function testSetArticleCategoryIdSuccess()
    {
        $id = 55;
        $entity = new ArticleCategoryRelationshipEntity();
        $service = $this->container->getByType(ArticleCategoryRelationshipSetServiceFactory::class)
            ->create();
        $service->setArticleCategoryId($entity, $id);

        Assert::equal($id, $entity->getArticleCategoryId());
    }



    public function testSetArticleCategoryIdFail()
    {
        $id = 55;
        $entity = new ArticleCategoryRelationshipEntity();
        $service = $this->container->getByType(ArticleCategoryRelationshipSetServiceFactory::class)
            ->create();
        $service->setArticleCategoryId($entity, $id);

        Assert::notEqual(44, $entity->getArticleCategoryId());
    }

}

(new ArticleCategoryRelationshipSetService())->run();
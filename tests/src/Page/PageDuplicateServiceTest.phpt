<?php

namespace App\Tests\Page;

require_once __DIR__ . "/../bootstrap.php";

use App\Page\PageDuplicateServiceException;
use App\Page\PageEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PageDuplicateServiceTest extends BaseTestCase
{


    public function testCheck()
    {
        $entity1 = new PageEntity();
        $entity1->setId(5);
        $entity1->setName("Článek");
        $entity1->setUrl("clanek-url");

        $entity2 = new PageEntity();
        $entity2->setId(7);
        $entity2->setName("Článek");
        $entity2->setUrl("clanek-url");

        $entity3 = new PageEntity();
        $entity3->setName("Článek 3");

        $service = new \App\Page\PageDuplicateService();

        //Check name
        Assert::exception(function () use ($entity1, $entity2, $service) {
            $service->checkName($entity1, $entity2);
        }, PageDuplicateServiceException::class);

        //Check url
        Assert::exception(function () use ($entity1, $entity2, $service) {
            $service->checkUrl($entity1, $entity2);
        }, PageDuplicateServiceException::class);

        Assert::type(PageEntity::class, $service->checkName($entity1, $entity3));
        Assert::type(PageEntity::class, $service->checkUrl($entity1, $entity3));
    }
}

(new PageDuplicateServiceTest())->run();
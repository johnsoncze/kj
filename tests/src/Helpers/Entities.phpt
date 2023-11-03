<?php

namespace App\Helpers\Tests;

require_once __DIR__ . "/../bootstrap.php";

use App\ArticleCategory\ArticleCategoryEntity;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Entities extends BaseTestCase
{


    public function testSearchValueSuccess()
    {
        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(1);
        $entity1->setName("Novinečky");
        $entity1->setUrl("url-adresa-entita-1");
        $entity1->setLanguageId(5);

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity2->setName("Novinečky 2");
        $entity2->setUrl("url-adresa-entita-2");
        $entity2->setLanguageId(50);

        $entity3 = new ArticleCategoryEntity();
        $entity3->setId(3);
        $entity3->setName("Novinečky 3");
        $entity3->setUrl("url-adresa-entita-3");
        $entity3->setLanguageId(500);

        $array = [$entity1, $entity2, $entity3];

        $result1 = \App\Helpers\Entities::searchValues($array, [2, 3], "id");
        $result2 = \App\Helpers\Entities::searchValues($array, [500, 1500], "languageId");
        $result3 = \App\Helpers\Entities::searchValues($array, [], "name");

        //result1
        Assert::count(2, $result1[\App\Helpers\Entities::VALUE_FOUND]);
        Assert::count(1, $result1[\App\Helpers\Entities::ENTITY_WITHOUT_VALUE]);
        Assert::error(function () use ($result1) {
            $result1[\App\Helpers\Entities::VALUE_NOT_FOUND];
        }, E_NOTICE);

        //result2
        Assert::count(1, $result2[\App\Helpers\Entities::VALUE_FOUND]);
        Assert::count(2, $result2[\App\Helpers\Entities::ENTITY_WITHOUT_VALUE]);
        Assert::count(1, $result2[\App\Helpers\Entities::VALUE_NOT_FOUND]);

        //result3
        Assert::count(3, $result3[\App\Helpers\Entities::ENTITY_WITHOUT_VALUE]);
        Assert::error(function () use ($result3) {
            $result3[\App\Helpers\Entities::VALUE_FOUND];
        }, E_NOTICE);
    }


    public function testSortById()
    {
        $entity1 = new ArticleCategoryEntity();
        $entity1->setId(1);
        $entity1->setLanguageId(1);
        $entity1->setSort(123);

        $entity2 = new ArticleCategoryEntity();
        $entity2->setId(2);
        $entity2->setLanguageId(1);
        $entity2->setSort(124);

        $entity3 = new ArticleCategoryEntity();
        $entity3->setId(3);
        $entity3->setLanguageId(1);
        $entity3->setSort(125);

        $entity4 = new ArticleCategoryEntity();
        $entity4->setLanguageId(4);

        $array = [1 => $entity1, 2 => $entity2, 3 => $entity3];

        \App\Helpers\Entities::sortById($array, [2,3,1]);

        Assert::same(11, (int)$entity2->getSort());
        Assert::same(12, (int)$entity3->getSort());
        Assert::same(13, (int)$entity1->getSort());
        Assert::same(40, (int)$entity4->getSort());
    }
}

(new Entities())->run();
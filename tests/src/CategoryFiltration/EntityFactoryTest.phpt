<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityFactoryTest extends BaseTestCase
{


    public function testCreateEntity()
    {
        $categoryId = 55;
        $groupId = 45;

        $entityFactory = new CategoryFiltrationEntityFactory();
        $entity = $entityFactory->create($categoryId, $groupId);

        Assert::same($categoryId, $entity->getCategoryId());
        Assert::same($groupId, $entity->getProductParameterGroupId());
    }
}

(new EntityFactoryTest())->run();
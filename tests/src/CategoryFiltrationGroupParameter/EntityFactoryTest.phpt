<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroupParameter;

use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class EntityFactoryTest extends BaseTestCase
{


    public function testCreate()
    {
        $groupId = 6;
        $parameterId = 8;

        $entityFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $entity = $entityFactory->create($groupId, $parameterId);

        Assert::same($groupId, $entity->getCategoryFiltrationGroupId());
        Assert::same($parameterId, $entity->getProductParameterId());
    }
}

(new EntityFactoryTest())->run();
<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterEntityFactoryTest extends BaseTestCase
{


    public function testCreateEntitySuccess()
    {
        $productParameterGroupId = 778;

        $factory = new ProductParameterEntityFactory();
        $entity = $factory->create($productParameterGroupId);

        Assert::type(ProductParameterEntity::class, $entity);
        Assert::same($productParameterGroupId, $entity->getProductParameterGroupId());
    }
}

(new ProductParameterEntityFactoryTest())->run();
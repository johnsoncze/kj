<?php

declare(strict_types = 1);

namespace App\Tests\Product\Parameter;

use App\Product\Parameter\ProductParameterFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterFactoryTest extends BaseTestCase
{


    public function testCreate()
    {
        $productId = 5;
        $parameterId = 10;

        $parameterFactory = new ProductParameterFactory();
        $parameter = $parameterFactory->create($productId, $parameterId);

        Assert::same($productId, $parameter->getProductId());
        Assert::same($parameterId, $parameter->getParameterId());
    }
}

(new ProductParameterFactoryTest())->run();
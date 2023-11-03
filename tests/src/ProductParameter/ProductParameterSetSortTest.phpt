<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterSetSort;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterSetSortTest extends BaseTestCase
{


    public function testSetSortSuccess()
    {
        $parameterFactory = new ProductParameterEntityFactory();
        $parameter = $parameterFactory->create(123);

        $sort = 12;
        $sortSetter = new ProductParameterSetSort();
        $sortSetter->set($parameter, $sort);

        Assert::same($sort, $parameter->getSort());
    }
}

(new ProductParameterSetSortTest())->run();
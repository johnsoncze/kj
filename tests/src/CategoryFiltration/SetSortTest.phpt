<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\CategoryFiltration\CategoryFiltrationSetSort;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SetSort extends BaseTestCase
{


    public function testSet()
    {
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $filtration = $filtrationFactory->create(5, 10);

        $sort = new CategoryFiltrationSetSort();
        $sort->setSort($filtration, 5);

        $clonedFiltration = clone $filtration;
        $sort->setSort($clonedFiltration, 66);

        Assert::same(5, $filtration->getSort());
        Assert::same(66, $clonedFiltration->getSort());
    }
}

(new SetSort())->run();
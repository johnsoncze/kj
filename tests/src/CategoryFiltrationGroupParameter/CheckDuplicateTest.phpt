<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroupParameter;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupParameterCheckDuplicate;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterCheckDuplicateException;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CheckDuplicateTest extends BaseTestCase
{


    public function testCheckWithoutDuplicateParameters()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(2, 1),
            $parameterFactory->create(2, 2),
            $parameterFactory->create(2, 3)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::same($parameters, $checker->check($parameters));
    }



    public function testCheckWithNotDuplicateParameters()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];
        $parameters2 = [
            $parameterFactory->create(2, 1),
            $parameterFactory->create(2, 3),
            $parameterFactory->create(2, 4)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::same($parameters, $checker->check($parameters, $parameters2));
    }



    public function testCheckWithAlmostDuplicateParameters()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];
        $parameters2 = [
            $parameterFactory->create(2, 1),
            $parameterFactory->create(2, 2),
            $parameterFactory->create(2, 3),
            $parameterFactory->create(2, 4)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::same($parameters, $checker->check($parameters, $parameters2));
    }



    public function testCheckWithDuplicateParametersInAnotherSorting()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];
        $parameters2 = [
            $parameterFactory->create(2, 2),
            $parameterFactory->create(2, 1),
            $parameterFactory->create(2, 3)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::exception(function () use ($parameters, $parameters2, $checker) {
            $checker->check($parameters, $parameters2);
        }, CategoryFiltrationGroupParameterCheckDuplicateException::class);
    }



    public function testCheckWithDuplicateParameters()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];
        $parameters2 = [
            $parameterFactory->create(2, 1),
            $parameterFactory->create(2, 2),
            $parameterFactory->create(2, 3)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::exception(function () use ($parameters, $parameters2, $checker) {
            $checker->check($parameters, $parameters2);
        }, CategoryFiltrationGroupParameterCheckDuplicateException::class);
    }



    public function testCheckWithDuplicateParametersFromSameGroup()
    {
        $parameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $parameters = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];
        $parameters2 = [
            $parameterFactory->create(1, 1),
            $parameterFactory->create(1, 2),
            $parameterFactory->create(1, 3)
        ];

        $checker = new CategoryFiltrationGroupParameterCheckDuplicate();

        Assert::same($parameters, $checker->check($parameters, $parameters2));
    }
}

(new CheckDuplicateTest())->run();
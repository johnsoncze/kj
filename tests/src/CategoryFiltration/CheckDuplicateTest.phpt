<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

require_once __DIR__ . '/../bootstrap.php';

use App\CategoryFiltration\CategoryFiltrationCheckDuplicate;
use App\CategoryFiltration\CategoryFiltrationCheckDuplicateException;
use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CheckDuplicateTest extends BaseTestCase
{


    public function testCheckWithoutDuplicateFiltration()
    {
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $filtration = $filtrationFactory->create(5, 10);

        $checker = new CategoryFiltrationCheckDuplicate();

        Assert::type(CategoryFiltrationEntity::class, $checker->check($filtration));
    }



    public function testCheckWithSecondFiltration()
    {
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $filtration = $filtrationFactory->create(5, 10);
        $filtration2 = $filtrationFactory->create(10, 10);

        $checker = new CategoryFiltrationCheckDuplicate();

        Assert::type(CategoryFiltrationEntity::class, $checker->check($filtration, $filtration2));
    }



    public function testCheckWithSameFiltration()
    {
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $filtration = $filtrationFactory->create(5, 10);
        $filtration->setId(4);

        $checker = new CategoryFiltrationCheckDuplicate();

        Assert::type(CategoryFiltrationEntity::class, $checker->check($filtration, $filtration));

    }



    public function testCheckWithDuplicateFiltration()
    {
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $filtration = $filtrationFactory->create(5, 10);
        $filtration->setId(4);
        $filtration2 = $filtrationFactory->create(5, 10);

        $checker = new CategoryFiltrationCheckDuplicate();

        Assert::exception(function () use ($checker, $filtration, $filtration2) {
            $checker->check($filtration, $filtration2);
        }, CategoryFiltrationCheckDuplicateException::class, "Filtrace je již nastavena.");
    }
}

(new CheckDuplicateTest())->run();
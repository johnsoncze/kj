<?php

declare(strict_types = 1);

namespace App\Tests\Article\Module;

use App\Article\Module\Module;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
trait ModuleTestTrait
{


    /**
     * @return Module
     */
    private function createTestModule() : Module
    {
        $module = new Module();
        $module->setName('Module 1');

        return $module;
    }
}
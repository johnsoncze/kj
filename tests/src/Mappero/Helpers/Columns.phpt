<?php

namespace App\Tests\Mappero\Helpers;

require_once __DIR__ . "/../../bootstrap.php";

use App\Tests\BaseTestCase;
use App\User\UserEntity;
use Ricaefeliz\Mappero\Exceptions\HelperException;
use Tester\Assert;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Columns extends BaseTestCase
{


    public function testGetColumn()
    {
        $annotation = UserEntity::getAnnotation();
        $columns = \Ricaefeliz\Mappero\Helpers\Columns::getColumns($annotation, ["id", "name", "email"]);
        $columns2 = \Ricaefeliz\Mappero\Helpers\Columns::getColumns($annotation);

        Assert::same($columns[0]->getName(), "u_id");
        Assert::same($columns[1]->getName(), "u_name");
        Assert::same($columns[2]->getName(), "u_email");

        Assert::same($columns2[0]->getName(), "u_id");
        Assert::same($columns2[1]->getName(), "u_name");
        Assert::same($columns2[2]->getName(), "u_email");
        Assert::same($columns2[3]->getName(), "u_password");
        Assert::same($columns2[4]->getName(), "u_role");
        Assert::same($columns2[5]->getName(), "u_add_date");

        Assert::exception(function () use ($annotation) {
            \Ricaefeliz\Mappero\Helpers\Columns::getColumns($annotation, ["unknownProperty"]);
        }, HelperException::class);
    }
}

(new \App\Tests\Mappero\Helpers\Columns())->run();
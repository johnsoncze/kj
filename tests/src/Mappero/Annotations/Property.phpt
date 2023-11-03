<?php

namespace App\Tests\Mappero\Annotations;

require_once __DIR__ . "/../../bootstrap.php";

use App\Tests\BaseTestCase;
use Ricaefeliz\Mappero\Annotations\Column;
use Ricaefeliz\Mappero\Annotations\PropertyException;
use Ricaefeliz\Mappero\Annotations\Relation;
use Tester\Assert;
use Tracy\Debugger;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Property extends BaseTestCase
{


    public function testCreateProperty()
    {
        $name = "propertyName";
        $relationEntityName = "relationEntityName";

        $relation = new Relation($name, $relationEntityName, Relation::ONE_TO_MANY);

        $column = new Column("columnName");
        $property1 = new \Ricaefeliz\Mappero\Annotations\Property($name);
        $property1->setColumn($column);

        $property2 = new \Ricaefeliz\Mappero\Annotations\Property($name);
        $property2->setRelation($relation);

        //Property 1
        Assert::same($property1->getName(), $name);
        Assert::same($property1->getColumn()->getName(), $column->getName());

        Assert::exception(function () use ($property1, $relation) {
            $property1->setRelation($relation);
        }, PropertyException::class);

        //Property 2
        Assert::type(Relation::class, $property2->getRelation());
        Assert::same($name, $property2->getRelation()->getEntity());
        Assert::same($relationEntityName, $property2->getRelation()->getRelationEntity());
        Assert::same(Relation::ONE_TO_MANY, $property2->getRelation()->getRelation());

        Assert::exception(function () use ($property2) {
            $column = new Column("columnName2");
            $property2->setColumn($column);
        }, PropertyException::class);
    }
}

(new Property())->run();
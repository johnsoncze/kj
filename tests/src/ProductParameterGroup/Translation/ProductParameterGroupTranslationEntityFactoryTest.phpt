<?php

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntity;
use App\ProductParameterGroup\ProductParameterGroupTranslationEntityFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../../bootstrap.php";

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupTranslationEntityFactoryTest extends BaseTestCase
{


    public function testSuccess()
    {
        $group = new ProductParameterGroupEntity();
        $group->setId(455);

        $languageId = 77;
        $name = "Materials of products";
        $filtrationTitle = "Material";

        $factory = new ProductParameterGroupTranslationEntityFactory();
        $entity = $factory->create($group->getId(), $languageId, $name, $filtrationTitle);

        Assert::type(ProductParameterGroupTranslationEntity::class, $entity);
        Assert::same($group->getId(), $entity->getProductParameterGroupId());
        Assert::same($languageId, $entity->getLanguageId());
        Assert::same($name, $entity->getName());
        Assert::same($filtrationTitle, $entity->getFiltrationTitle());
    }
}

(new ProductParameterGroupTranslationEntityFactoryTest())->run();
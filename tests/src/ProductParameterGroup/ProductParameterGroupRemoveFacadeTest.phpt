<?php

namespace App\Tests\ProductParameterGroup;

use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupNotFoundException;
use App\ProductParameterGroup\ProductParameterGroupRemoveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterGroupRemoveFacadeTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;

    /** @var ProductParameterGroupRepository|null */
    protected $groupRepository;

    /** @var ProductParameterGroupEntity|null */
    protected $group;



    protected function setUp()
    {
        parent::setUp();

        //save a new entity
        $this->group = $this->createTestProductParameterGroup();

        $this->groupRepository = $this->container->getByType(ProductParameterGroupRepositoryFactory::class)->create();
        $this->groupRepository->save($this->group);
    }



    public function testSuccess()
    {
        //remove group
        $removeFacade = $this->container->getByType(ProductParameterGroupRemoveFacadeFactory::class)->create();
        $removeFacade->remove($this->group->getId());

        //try load group from storage
        Assert::exception(function () {
            $this->groupRepository->getOneById($this->group->getId());
        }, ProductParameterGroupNotFoundException::class);
    }
}

(new ProductParameterGroupRemoveFacadeTest())->run();
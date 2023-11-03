<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameter\ProductParameterSaveFacadeFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use App\Tests\ProductParameterGroup\ProductParameterGroupTestTrait;
use Tester\Assert;


require_once __DIR__ . "/../bootstrap.php";

\Tester\Environment::lock("database", TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class ProductParameterAddTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;

    /** @var ProductParameterGroupRepository|null */
    protected $groupRepository;

    /** @var ProductParameterGroupEntity|null */
    protected $group1;

    /** @var ProductParameterEntity[]|array */
    protected $parameters = [];



    public function setUp()
    {
        parent::setUp();

        //save a test groups
        $this->group1 = $group = $this->createTestProductParameterGroup();
        $groupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->groupRepository = $groupRepository = $groupRepositoryFactory->create();
        $groupRepository->save($group);
    }



    public function testAddSuccess()
    {
        $saveFacadeFactory = $this->container->getByType(ProductParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $this->parameters[] = $parameter = $saveFacade->add($this->group1);

        //load parameter from storage
        $productParameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $productParameterRepository = $productParameterRepositoryFactory->create();
        $parameterFromStorage = $productParameterRepository->getOneById($parameter->getId());

        //tests that method for add a parameter returns product parameter entity
        Assert::type(ProductParameterEntity::class, $parameter);
        Assert::same($this->group1->getId(), $parameter->getProductParameterGroupId());

        //tests that method for add a parameter saves into storage
        Assert::type(ProductParameterEntity::class, $parameterFromStorage);
        Assert::same($this->group1->getId(), $parameterFromStorage->getProductParameterGroupId());
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test groups
        $this->groupRepository->remove($this->group1);

        //remove test parameters
        $parameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $parameterRepository = $parameterRepositoryFactory->create();
        foreach ($this->parameters as $parameter) {
            $parameterRepository->remove($parameter);
        }
    }
}

(new ProductParameterAddTest())->run();
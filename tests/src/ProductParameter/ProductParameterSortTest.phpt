<?php

namespace App\Tests\ProductParameter;

use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameter\ProductParameterSortFacadeException;
use App\ProductParameter\ProductParameterSortFacadeFactory;
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
class ProductParameterSortTest extends BaseTestCase
{


    use ProductParameterGroupTestTrait;


    /** @var ProductParameterGroupRepository|null */
    protected $groupRepository;

    /** @var ProductParameterRepository|null */
    protected $parameterRepository;

    /** @var ProductParameterGroupEntity|null */
    protected $group;

    /** @var ProductParameterEntity[]|array */
    protected $parameters = [];



    public function setUp()
    {
        parent::setUp();

        //save test group
        $this->group = $group = $this->createTestProductParameterGroup();
        $groupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->groupRepository = $groupRepository = $groupRepositoryFactory->create();
        $groupRepository->save($group);

        //save tests parameters of test group
        $parameterFactory = new ProductParameterEntityFactory();
        $this->parameters[] = $parameter1 = $parameterFactory->create($group->getId());
        $this->parameters[] = $parameter2 = $parameterFactory->create($group->getId());
        $this->parameters[] = $parameter3 = $parameterFactory->create($group->getId());
        $parameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->parameterRepository = $parameterRepository = $parameterRepositoryFactory->create();
        $parameterRepository->save($this->parameters);
    }



    public function testSortSuccess()
    {
        $sort = [
            1 => $this->parameters[1]->getId(),
            2 => $this->parameters[2]->getId(),
            3 => $this->parameters[0]->getId()
        ];

        //sort facade
        $sortFacadeFactory = $this->container->getByType(ProductParameterSortFacadeFactory::class);
        $sortFacade = $sortFacadeFactory->create();

        //save sort
        $sortFacade->saveSort($this->group, $sort);

        //load sorted parameters from storage
        $parametersFromStorage = $this->parameterRepository->findByProductParameterGroupId($this->group->getId());

        Assert::count(count($sort), $parametersFromStorage);
        Assert::same(array_values($sort), array_keys($parametersFromStorage));
    }



    public function testSortingUnknownParameters()
    {
        //sort facade
        $sortFacadeFactory = $this->container->getByType(ProductParameterSortFacadeFactory::class);
        $sortFacade = $sortFacadeFactory->create();

        Assert::exception(function () use ($sortFacade) {
            $sortFacade->saveSort($this->group, [1 => 444788899, 2 => 7897899746]);
        }, ProductParameterSortFacadeException::class);
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove tests data
        foreach ($this->parameters as $parameter) {
            $this->parameterRepository->remove($parameter);
        }
        $this->groupRepository->remove($this->group);
    }
}

(new ProductParameterSortTest())->run();
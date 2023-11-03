<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroup;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupNotFoundException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRemoveFacadeException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRemoveFacadeFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RemoveFacadeTest extends BaseTestCase
{


    use CategoryFiltrationGroupTestTrait;

    /** @var CategoryFiltrationGroupEntity|null */
    protected $group;

    /** @var CategoryFiltrationGroupRepository|null */
    protected $groupRepo;



    public function setUp()
    {
        parent::setUp();

        //save a test group
        $this->group = $this->createTestGroup();
        $groupRepoFactory = $this->container->getByType(CategoryFiltrationGroupRepositoryFactory::class);
        $this->groupRepo = $groupRepoFactory->create();

        $this->saveWithoutForeignKeysCheck($this->group, $this->groupRepo);
    }



    public function testRemove()
    {
        $removeFacadeFactory = $this->container->getByType(CategoryFiltrationGroupRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();
        $removeFacade->remove((int)$this->group->getId());

        Assert::exception(function () {
            $this->groupRepo->getOneById($this->group->getId());
        }, CategoryFiltrationGroupNotFoundException::class);
    }



    public function testRemoveNotExistsGroup()
    {
        $id = 777;
        $removeFacadeFactory = $this->container->getByType(CategoryFiltrationGroupRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();

        Assert::exception(function () use ($removeFacade, $id) {
            $removeFacade->remove($id);
        }, CategoryFiltrationGroupRemoveFacadeException::class, sprintf("Group of category filtration with id '%s' not found.", $id));
    }



    public function tearDown()
    {
        //try remove test group
        $this->groupRepo->remove($this->group);
    }
}

(new RemoveFacadeTest())->run();
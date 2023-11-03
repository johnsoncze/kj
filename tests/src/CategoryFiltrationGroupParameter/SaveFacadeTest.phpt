<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltrationGroupParameter;

use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntityFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepository;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepositoryFactory;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterSaveFacadeException;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterSaveFacadeFactory;
use App\Helpers\Entities;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\Tests\BaseTestCase;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class SaveFacadeTest extends BaseTestCase
{


    /** @var ProductParameterEntity[]|null */
    protected $parameters;

    /** @var CategoryFiltrationGroupEntity[]|null */
    protected $groups;

    /** @var CategoryFiltrationGroupParameterEntity[]|null */
    protected $groupParameters;

    /** @var ProductParameterRepository|null */
    protected $parameterRepo;

    /** @var CategoryFiltrationGroupRepository|null */
    protected $groupRepo;

    /** @var CategoryFiltrationGroupParameterRepository|null */
    protected $groupParameterRepo;



    public function setUp()
    {
        parent::setUp();

        //save test parameters
        $groupId = 100;
        $parameterFactory = new ProductParameterEntityFactory();
        $this->parameters[] = $parameterFactory->create($groupId);
        $this->parameters[] = $parameterFactory->create($groupId);
        $this->parameters[] = $parameterFactory->create($groupId);
        $this->parameters[] = $parameterFactory->create($groupId);
        $this->parameters[] = $parameterFactory->create($groupId);
        $this->parameters[] = $parameterFactory->create($groupId);
        $parameterRepoFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->parameterRepo = $parameterRepoFactory->create();

        //save test groups of category parameters
        $groupFactory = new CategoryFiltrationGroupEntityFactory();
        $this->groups[] = $groupFactory->create(555, NULL, NULL, NULL, TRUE, TRUE, TRUE, CategoryFiltrationGroupEntity::DRAFT);
        $this->groups[] = $groupFactory->create(555, NULL, NULL, NULL, TRUE, TRUE, TRUE, CategoryFiltrationGroupEntity::DRAFT);
        $this->groups[] = $groupFactory->create(777, NULL, NULL, NULL, TRUE, TRUE, TRUE, CategoryFiltrationGroupEntity::DRAFT);
        $groupRepoFactory = $this->container->getByType(CategoryFiltrationGroupRepositoryFactory::class);
        $this->groupRepo = $groupRepoFactory->create();

        $db = $this->container->getByType(Context::class);

        $db->query('SET foreign_key_checks = 0');
        $this->parameterRepo->save($this->parameters);
        $this->groupRepo->save($this->groups);
        $db->query('SET foreign_key_checks = 1');

        $groupParameterRepoFactory = $this->container->getByType(CategoryFiltrationGroupParameterRepositoryFactory::class);
        $this->groupParameterRepo = $groupParameterRepoFactory->create();
    }



    public function testSave()
    {
        /** @var $saveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $group = $this->groups[0];
        $parameters = [$this->parameters[1]->getId(), $this->parameters[2]->getId(), $this->parameters[3]->getId()];

        $this->groupParameters = $savedParameters = $saveFacade->save($group, $parameters);

        //load parameters from storage
        $parametersFromStorage = $this->groupParameterRepo->findByCategoryFiltrationGroupId($group->getId());

        Assert::count(count($parameters), $savedParameters);
        Assert::same(array_values($parameters), Entities::getProperty($savedParameters, 'productParameterId'));

        Assert::count(count($parameters), $parametersFromStorage);
        Assert::same(array_values($parameters), Entities::getProperty($parametersFromStorage, 'productParameterId'));
    }



    public function testUpdate()
    {
        $group = $this->groups[0];
        $parameters = [$this->parameters[1]->getId(), $this->parameters[2]->getId(), $this->parameters[3]->getId()];

        /** @var $saveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $saveFacade->save($group, $parameters);

        //update
        $updateParameters = [$this->parameters[4]->getId(), $this->parameters[0]->getId(), $this->parameters[3]->getId()];
        $this->groupParameters = $savedParameters = $saveFacade->save($group, $updateParameters);
        $savedParametersId = Entities::getProperty($savedParameters, 'productParameterId');

        //load parameters from storage
        $parametersFromStorage = $this->groupParameterRepo->findByCategoryFiltrationGroupId($group->getId());
        $parametersId = Entities::getProperty($parametersFromStorage, 'productParameterId');

        Assert::count(count($updateParameters), $savedParameters);
        Assert::falsey(array_diff($updateParameters, $savedParametersId) || array_diff($parametersId, $savedParametersId));

        Assert::count(count($updateParameters), $parametersFromStorage);
        Assert::falsey(array_diff($updateParameters, $parametersId) || array_diff($parametersId, $updateParameters));
    }



    public function testSaveWithDuplicateParameters()
    {
        $parameters = [$this->parameters[1]->getId(), $this->parameters[2]->getId(), $this->parameters[3]->getId()];

        /**
         * Save test parameters
         * @var $saveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory
         */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->groupParameters = $saveFacade->save($this->groups[0], $parameters);

        Assert::exception(function () use ($saveFacade, $parameters) {
            $saveFacade->save($this->groups[1], $parameters);
        }, CategoryFiltrationGroupParameterSaveFacadeException::class, 'Kombinace parametrů již existuje.');

        Assert::exception(function () use ($saveFacade, $parameters) {
            $saveFacade->save($this->groups[1], [$parameters[1], $parameters[2], $parameters[0]]);
        }, CategoryFiltrationGroupParameterSaveFacadeException::class, 'Kombinace parametrů již existuje.');
    }



    public function testSaveWithDuplicateParametersForAnotherCategory()
    {
        $parameters = [$this->parameters[1]->getId(), $this->parameters[2]->getId(), $this->parameters[3]->getId()];

        /**
         * Save test parameters
         * @var $saveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory
         */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->groupParameters = $saveFacade->save($this->groups[0], $parameters);

        $savedParameters = $saveFacade->save($this->groups[2], $parameters);
        $this->groupParameters = array_merge($this->groupParameters, $savedParameters);

        Assert::count(count($parameters), $savedParameters);
    }



    public function testSaveWithNotExistsParameters()
    {
        $parameters = [$this->parameters[1]->getId() + 100, $this->parameters[2]->getId() + 100];

        /**
         * Save test parameters
         * @var $saveFacadeFactory CategoryFiltrationGroupParameterSaveFacadeFactory
         */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationGroupParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        Assert::exception(function () use ($saveFacade, $parameters) {
            $saveFacade->save($this->groups[0], $parameters);
        }, CategoryFiltrationGroupParameterSaveFacadeException::class, 'Některý parametr již neexistuje. Není možné uložit kombinaci parametrů.');
    }



    public function tearDown()
    {
        parent::tearDown();

        if ($this->groupParameters) {
            $this->groupParameterRepo->remove($this->groupParameters);
        }
        $this->groupRepo->remove($this->groups);
        $this->parameterRepo->remove($this->parameters);

        $this->groups = NULL;
        $this->parameters = NULL;
        $this->groupParameters = NULL;
    }
}

(new SaveFacadeTest())->run();
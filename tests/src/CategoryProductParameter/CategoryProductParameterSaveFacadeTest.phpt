<?php

declare(strict_types = 1);

namespace App\Tests\CategoryProductParameter;

use App\Category\CategoryEntity;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\CategoryProductParameter\CategoryProductParameterEntity;
use App\CategoryProductParameter\CategoryProductParameterRepository;
use App\CategoryProductParameter\CategoryProductParameterRepositoryFactory;
use App\CategoryProductParameter\CategoryProductParameterSaveFacadeFactory;
use App\Helpers\Entities;
use App\ProductParameter\ProductParameterEntity;
use App\ProductParameter\ProductParameterEntityFactory;
use App\ProductParameter\ProductParameterRepository;
use App\ProductParameter\ProductParameterRepositoryFactory;
use App\ProductParameterGroup\ProductParameterGroupEntity;
use App\ProductParameterGroup\ProductParameterGroupRepository;
use App\ProductParameterGroup\ProductParameterGroupRepositoryFactory;
use App\Tests\BaseTestCase;
use App\Tests\Category\CategoryTestTrait;
use App\Tests\ProductParameterGroup\ProductParameterGroupTestTrait;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class CategoryProductParameterSaveFacadeTest extends BaseTestCase
{


    use CategoryTestTrait;
    use ProductParameterGroupTestTrait;


    /** @var CategoryRepository|null */
    protected $categoryRepository;

    /** @var ProductParameterGroupRepository|null */
    protected $productParameterGroupRepository;

    /** @var ProductParameterRepository|null */
    protected $productParameterRepository;

    /** @var CategoryProductParameterRepository|null */
    protected $categoryParameterRepository;

    /** @var CategoryEntity|null */
    protected $categoryEntity;

    /** @var ProductParameterGroupEntity|null */
    protected $parameterGroupEntity;

    /** @var array|ProductParameterEntity[] */
    protected $parameterEntities = [];

    /** @var array|CategoryProductParameterEntity[] */
    protected $categoryParameterEntities = [];



    public function setUp()
    {
        parent::setUp();

        //save test category
        $this->categoryEntity = $this->createTestCategory();

        $categoryRepositoryFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepository = $categoryRepositoryFactory->create();
        $this->categoryRepository->save($this->categoryEntity);

        //save test group of product parameters
        $this->parameterGroupEntity = $this->createTestProductParameterGroup();

        $groupRepositoryFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->productParameterGroupRepository = $groupRepositoryFactory->create();
        $this->productParameterGroupRepository->save($this->parameterGroupEntity);

        //save test product parameters
        $parameterFactory = new ProductParameterEntityFactory();
        $this->parameterEntities[] = $parameter1 = $parameterFactory->create($this->parameterGroupEntity->getId());
        $this->parameterEntities[] = $parameter2 = $parameterFactory->create($this->parameterGroupEntity->getId());
        $this->parameterEntities[] = $parameter3 = $parameterFactory->create($this->parameterGroupEntity->getId());
        $this->parameterEntities[] = $parameter4 = $parameterFactory->create($this->parameterGroupEntity->getId());

        $parameterRepositoryFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->productParameterRepository = $parameterRepositoryFactory->create();
        $this->productParameterRepository->save([$parameter1, $parameter2, $parameter3, $parameter4]);
    }



    public function testSaveNew()
    {
        //save
        $saveFacadeFactory = $this->container->getByType(CategoryProductParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $parametersId = [$this->parameterEntities[1]->getId(), $this->parameterEntities[2]->getId()];

        $saveFacade->save($this->categoryEntity, $parametersId);

        //load parameters from storage
        $categoryParameterRepositoryFactory = $this->container->getByType(CategoryProductParameterRepositoryFactory::class);
        $this->categoryParameterRepository = $categoryParameterRepositoryFactory->create();
        $this->categoryParameterEntities = $parametersFromStorage = $this->categoryParameterRepository->findByCategoryId($this->categoryEntity->getId());

        Assert::count(count($parametersId), $parametersFromStorage);
        Assert::same($parametersId, Entities::getProperty($parametersFromStorage, 'productParameterId'));
    }



    public function testUpdateWithDifferentParameters()
    {
        //save default
        $saveFacadeFactory = $this->container->getByType(CategoryProductParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $parametersId = [$this->parameterEntities[1]->getId(), $this->parameterEntities[2]->getId()];

        $saveFacade->save($this->categoryEntity, $parametersId);

        //update
        $newParametersId = [$this->parameterEntities[0]->getId(), $this->parameterEntities[1]->getId(), $this->parameterEntities[3]->getId()];

        $saveFacade->save($this->categoryEntity, $newParametersId);

        //load parameters from storage
        $categoryParameterRepositoryFactory = $this->container->getByType(CategoryProductParameterRepositoryFactory::class);
        $this->categoryParameterRepository = $categoryParameterRepositoryFactory->create();
        $this->categoryParameterEntities = $parametersFromStorage = $this->categoryParameterRepository->findByCategoryId($this->categoryEntity->getId());

        Assert::count(count($newParametersId), $parametersFromStorage);
        Assert::falsey(array_diff($newParametersId, Entities::getProperty($parametersFromStorage, 'productParameterId')));
    }



    public function testUpdateWithSameParameters()
    {
        //save default
        $saveFacadeFactory = $this->container->getByType(CategoryProductParameterSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();

        $parametersId = [$this->parameterEntities[1]->getId(), $this->parameterEntities[2]->getId()];

        $saveFacade->save($this->categoryEntity, $parametersId);
        $saveFacade->save($this->categoryEntity, $parametersId);

        //load parameters from storage
        $categoryParameterRepositoryFactory = $this->container->getByType(CategoryProductParameterRepositoryFactory::class);
        $this->categoryParameterRepository = $categoryParameterRepositoryFactory->create();
        $this->categoryParameterEntities = $parametersFromStorage = $this->categoryParameterRepository->findByCategoryId($this->categoryEntity->getId());

        Assert::count(count($parametersId), $parametersFromStorage);
        Assert::falsey(array_diff($parametersId, Entities::getProperty($parametersFromStorage, 'productParameterId')));
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove test parameters
        foreach ($this->parameterEntities as $parameter) {
            $this->productParameterRepository->remove($parameter);
        }
        $this->parameterEntities = [];

        //remove test group
        $this->productParameterGroupRepository->remove($this->parameterGroupEntity);

        //remove test category
        $this->categoryRepository->remove($this->categoryEntity);

        //remove category parameters
        foreach ($this->categoryParameterEntities as $categoryParameterEntity) {
            $this->categoryParameterRepository->remove($categoryParameterEntity);
        }
        $this->categoryParameterEntities = [];
    }
}

(new CategoryProductParameterSaveFacadeTest())->run();
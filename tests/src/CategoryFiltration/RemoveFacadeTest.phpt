<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

use App\Category\CategoryEntity;
use App\Category\CategoryEntityFactory;
use App\Category\CategoryFiltrationRepository;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\CategoryFiltration\CategoryFiltrationNotFoundException;
use App\CategoryFiltration\CategoryFiltrationRemoveFacadeFactory;
use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntity;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupEntityFactory;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupNotFoundException;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepository;
use App\CategoryFiltrationGroup\CategoryFiltrationGroupRepositoryFactory;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntity;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterEntityFactory;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepository;
use App\CategoryFiltrationGroupParameter\CategoryFiltrationGroupParameterRepositoryFactory;
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
use App\Tests\CategoryFiltrationGroup\CategoryFiltrationGroupTestTrait;
use App\Tests\ProductParameterGroup\ProductParameterGroupTestTrait;
use Nette\Database\Context;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', TEMP_TEST);

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RemoveFacadeTest extends BaseTestCase
{
    use CategoryFiltrationGroupTestTrait;
    use CategoryTestTrait;
    use ProductParameterGroupTestTrait;

    /** @var ProductParameterGroupEntity|null */
    protected $productParameterGroup;

    /** @var ProductParameterEntity[]|array */
    protected $productParameters = [];

    /** @var CategoryEntity|null */
    protected $category;

    /** @var CategoryFiltrationEntity|null */
    protected $categoryFiltration;

    /** @var CategoryFiltrationGroupEntity|null */
    protected $categoryFiltrationGroup;

    /** @var CategoryFiltrationGroupEntity|null */
    protected $secondCategoryFiltrationGroup;

    /** @var CategoryFiltrationGroupParameterEntity[]|null */
    protected $categoryFiltrationGroupParameters;

    /** @var CategoryFiltrationGroupParameterEntity[]|null */
    protected $secondCategoryFiltrationGroupParameters;

    /** ------- Repositories ------- **/

    /** @var ProductParameterGroupRepository|null */
    protected $productParameterGroupRepo;

    /** @var ProductParameterRepository|null */
    protected $productParameterRepo;

    /** @var CategoryRepository|null */
    protected $categoryRepo;

    /** @var CategoryFiltrationRepository|null */
    protected $categoryFiltrationRepo;

    /** @var CategoryFiltrationGroupRepository|null */
    protected $categoryFiltrationGroupRepo;

    /** @var CategoryFiltrationGroupParameterRepository|null */
    protected $categoryFiltrationGroupParamRepo;



    public function setUp()
    {
        parent::setUp();

        //save test product parameter group
        $this->productParameterGroup = $this->createTestProductParameterGroup();
        $productParameterGroupRepoFactory = $this->container
            ->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->productParameterGroupRepo = $productParameterGroupRepoFactory->create();
        $this->productParameterGroupRepo->save($this->productParameterGroup);

        //save test product parameters
        $productParameterFactory = new ProductParameterEntityFactory();
        $this->productParameters[] = $parameter1 = $productParameterFactory->create($this->productParameterGroup->getId());
        $this->productParameters[] = $parameter2 = $productParameterFactory->create($this->productParameterGroup->getId());
        $this->productParameters[] = $parameter3 = $productParameterFactory->create($this->productParameterGroup->getId());
        $productParameterRepoFactory = $this->container->getByType(ProductParameterRepositoryFactory::class);
        $this->productParameterRepo = $productParameterRepoFactory->create();
        $this->productParameterRepo->save($this->productParameters);

        //save test category
        $this->category = $this->createTestCategory();
        $categoryRepoFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepo = $categoryRepoFactory->create();
        $this->categoryRepo->save($this->category);

        //save category filtration
        $categoryFiltrationFactory = new CategoryFiltrationEntityFactory();
        $this->categoryFiltration = $categoryFiltrationFactory->create($this->category->getId(), $this->productParameterGroup->getId());
        $categoryFiltrationRepoFactory = $this->container->getByType(CategoryFiltrationRepositoryFactory::class);
        $this->categoryFiltrationRepo = $categoryFiltrationRepoFactory->create();
        $this->categoryFiltrationRepo->save($this->categoryFiltration);

        //save test category filtration group
        $this->categoryFiltrationGroup = $this->createTestGroup();
        $this->categoryFiltrationGroup->setCategoryId($this->category->getId());
        $categoryFiltrationGroupRepoFactory = $this->container->getByType(CategoryFiltrationGroupRepositoryFactory::class);
        $this->categoryFiltrationGroupRepo = $categoryFiltrationGroupRepoFactory->create();
        $this->categoryFiltrationGroupRepo->save($this->categoryFiltrationGroup);

        //save second test category filtration group with another not exists category
        $this->secondCategoryFiltrationGroup = $this->createTestGroup();
        $this->secondCategoryFiltrationGroup->setCategoryId((int)($this->categoryFiltrationGroup->getId() + 20));
        $this->saveWithoutForeignKeysCheck($this->secondCategoryFiltrationGroup, $this->categoryFiltrationGroupRepo);

        //save test parameters of category filtration group
        $filtrationParameterFactory = new CategoryFiltrationGroupParameterEntityFactory();
        $this->categoryFiltrationGroupParameters[] = $filParam1 = $filtrationParameterFactory->create($this->categoryFiltrationGroup->getId(),
            $parameter1->getId());
        $this->categoryFiltrationGroupParameters[] = $filParam2 = $filtrationParameterFactory->create($this->categoryFiltrationGroup->getId(),
            $parameter2->getId());
        $categoryFilGroupParamRepoFactory = $this->container
            ->getByType(CategoryFiltrationGroupParameterRepositoryFactory::class);
        $this->categoryFiltrationGroupParamRepo = $categoryFilGroupParamRepoFactory->create();
        $this->categoryFiltrationGroupParamRepo->save($this->categoryFiltrationGroupParameters);

        //save parameters for second group
        $cloneFilParam1 = clone $filParam1;
        $cloneFilParam1->setId(NULL);
        $cloneFilParam1->setCategoryFiltrationGroupId($this->secondCategoryFiltrationGroup->getId());
        $cloneFilParam2 = clone $filParam2;
        $cloneFilParam2->setId(NULL);
        $cloneFilParam2->setCategoryFiltrationGroupId($this->secondCategoryFiltrationGroup->getId());
        $this->secondCategoryFiltrationGroupParameters = [$cloneFilParam1, $cloneFilParam2];
        $this->categoryFiltrationGroupParamRepo->save($this->secondCategoryFiltrationGroupParameters);
    }



    public function testRemove()
    {
        $removeFacadeFactory = $this->container->getByType(CategoryFiltrationRemoveFacadeFactory::class);
        $removeFacade = $removeFacadeFactory->create();
        $removeFacade->remove($this->categoryFiltration->getId());

        //load parameter group with same parameters of removed filtration but with another category
        $categoryFiltration = $this->categoryFiltrationGroupRepo->getOneById($this->secondCategoryFiltrationGroup->getId());
        $categoryFiltrationParameters = $this->categoryFiltrationGroupParamRepo->findByCategoryFiltrationGroupId($categoryFiltration->getId());

        //try load filtration from storage
        Assert::exception(function () {
            $this->categoryFiltrationRepo->getOneById($this->categoryFiltration->getId());
        }, CategoryFiltrationNotFoundException::class);

        //try load group of filtration
        Assert::exception(function () {
            $this->categoryFiltrationGroupRepo->getOneById($this->categoryFiltrationGroup->getId());
        }, CategoryFiltrationGroupNotFoundException::class);

        //check group with another category
        Assert::type(CategoryFiltrationGroupEntity::class, $categoryFiltration);
        Assert::same($this->secondCategoryFiltrationGroup->getId(), (int)$categoryFiltration->getId());
        Assert::same($this->secondCategoryFiltrationGroup->getCategoryId(), (int)$categoryFiltration->getCategoryId());

        //check parameters of group with another category
        Assert::count(2, $categoryFiltrationParameters);
        Assert::falsey(array_diff(Entities::getProperty($categoryFiltrationParameters, 'id'),
            Entities::getProperty($this->secondCategoryFiltrationGroupParameters, 'id')));
    }



    public function tearDown()
    {
        parent::tearDown();

        //remove second parameter group
        $this->categoryFiltrationGroupRepo->remove($this->secondCategoryFiltrationGroup);

        //remove test category
        $this->categoryRepo->remove($this->category);

        //remove test product parameter group
        $this->productParameterGroupRepo->remove($this->productParameterGroup);
    }
}

(new RemoveFacadeTest())->run();
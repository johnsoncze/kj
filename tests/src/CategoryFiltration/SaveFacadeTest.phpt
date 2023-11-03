<?php

declare(strict_types = 1);

namespace App\Tests\CategoryFiltration;

use App\Category\CategoryEntity;
use App\Category\CategoryFiltrationRepository;
use App\Category\CategoryRepository;
use App\Category\CategoryRepositoryFactory;
use App\CategoryFiltration\CategoryFiltrationEntity;
use App\CategoryFiltration\CategoryFiltrationEntityFactory;
use App\CategoryFiltration\CategoryFiltrationRepositoryFactory;
use App\CategoryFiltration\CategoryFiltrationSaveFacadeException;
use App\CategoryFiltration\CategoryFiltrationSaveFacadeFactory;
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
class SaveFacadeTest extends BaseTestCase
{


    use CategoryTestTrait;
    use ProductParameterGroupTestTrait;

    /** @var ProductParameterGroupEntity|null */
    protected $parameterGroup;

    /** @var CategoryEntity|null */
    protected $category;

    /** @var CategoryFiltrationEntity[]|array */
    protected $filtrations = [];

    /** --------- Repositories --------- */

    /** @var ProductParameterGroupRepository|null */
    protected $parameterGroupRepo;

    /** @var CategoryFiltrationRepository|null */
    protected $filtrationRepo;

    /** @var CategoryRepository|null */
    protected $categoryRepo;



    public function setUp()
    {
        parent::setUp();

        //save group of parameters
        $this->parameterGroup = $this->createTestProductParameterGroup();
        $parameterGroupRepoFactory = $this->container->getByType(ProductParameterGroupRepositoryFactory::class);
        $this->parameterGroupRepo = $parameterGroupRepoFactory->create();
        $this->parameterGroupRepo->save($this->parameterGroup);

        //save test category
        $this->category = $this->createTestCategory();
        $categoryRepoFactory = $this->container->getByType(CategoryRepositoryFactory::class);
        $this->categoryRepo = $categoryRepoFactory->create();
        $this->categoryRepo->save($this->category);

        $filtrationRepoFactory = $this->container->getByType(CategoryFiltrationRepositoryFactory::class);
        $this->filtrationRepo = $filtrationRepoFactory->create();
    }



    public function testSaveNew()
    {
        /** @var $saveFacadeFactory CategoryFiltrationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $filtration = $saveFacade->add($this->category, (int)$this->parameterGroup->getId(), FALSE, TRUE, TRUE);

        //load filtration from storage

        $filtrationFromStorage = $this->filtrationRepo->getOneById($filtration->getId());

        //check entity which returns method for save a new filtration
        Assert::type(CategoryFiltrationEntity::class, $filtration);
        Assert::same((int)$this->parameterGroup->getId(), (int)$filtration->getProductParameterGroupId());
        Assert::falsey($filtration->getIndexSeo());
        Assert::truthy($filtration->getFollowSeo());
        Assert::truthy($filtration->getSiteMap());

        //check entity from storage
        Assert::type(CategoryFiltrationEntity::class, $filtrationFromStorage);
        Assert::same((int)$this->parameterGroup->getId(), (int)$filtrationFromStorage->getProductParameterGroupId());
        Assert::falsey($filtrationFromStorage->getIndexSeo());
        Assert::truthy($filtrationFromStorage->getFollowSeo());
        Assert::truthy($filtrationFromStorage->getSiteMap());
    }



    public function testSaveDuplicateNew()
    {
        /** @var $saveFacadeFactory CategoryFiltrationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $this->filtrations[] = $saveFacade->add($this->category, (int)$this->parameterGroup->getId(), FALSE, TRUE, TRUE);

        Assert::exception(function () use ($saveFacade) {
            $saveFacade->add($this->category, (int)$this->parameterGroup->getId(), FALSE, TRUE, TRUE);
        }, CategoryFiltrationSaveFacadeException::class, 'Filtrace je již nastavena.');
    }



    public function testUpdate()
    {
        //save test filtration
        $filtrationFactory = new CategoryFiltrationEntityFactory();
        $this->filtrations[] = $filtration = $filtrationFactory->create($this->category->getId(),
            $this->parameterGroup->getId(), TRUE, TRUE, TRUE);
        $filtrationRepo = $this->container->getByType(CategoryFiltrationRepositoryFactory::class);
        $this->filtrationRepo = $filtrationRepo->create();
        $this->filtrationRepo->save($filtration);

        $filtration->setIndexSeo(TRUE);
        $filtration->setFollowSeo(TRUE);
        $filtration->setSiteMap(FALSE);

        /** @var $saveFacadeFactory CategoryFiltrationSaveFacadeFactory */
        $saveFacadeFactory = $this->container->getByType(CategoryFiltrationSaveFacadeFactory::class);
        $saveFacade = $saveFacadeFactory->create();
        $saveFacade->update($filtration);

        //load filtration from storage
        $filtrationFromStorage = $this->filtrationRepo->getOneById($filtration->getId());

        Assert::type(CategoryFiltrationEntity::class, $filtrationFromStorage);
        Assert::same((int)$this->parameterGroup->getId(), (int)$filtrationFromStorage->getProductParameterGroupId());
        Assert::truthy($filtrationFromStorage->getIndexSeo());
        Assert::truthy($filtrationFromStorage->getFollowSeo());
        Assert::falsey($filtrationFromStorage->getSiteMap());
    }



    public function tearDown()
    {
        parent::tearDown();

        foreach ($this->filtrations as $filtration) {
            $this->filtrationRepo->remove($filtration);
        }

        $this->categoryRepo->remove($this->category);
        $this->parameterGroupRepo->remove($this->parameterGroup);

        $this->filtrations = [];
        $this->category = NULL;
        $this->parameterGroup = NULL;
    }
}

(new SaveFacadeTest())->run();